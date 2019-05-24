<?php

namespace Erp\UserBundle\Controller;

use Erp\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Erp\CoreBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use \DateTime;

class StatementController extends BaseController {

    /**
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     */
    public function indexAction(Request $request, $_format = 'html') {
        try {
            $yearNow = (new DateTime)->format('Y');

            $year = $request->get('erp_form_statement_year', $yearNow);
            $month = $request->get('erp_form_statement_month', 0);

            $landlordId = $request->get('erp_form_statement_landlord', 0);
            
            /** @var $user \Erp\UserBundle\Entity\User */
            $user = $this->getUser();

            if ($landlordId == 0) {
                return $this->showGenericStatement($user, $month, $year, $_format, $yearNow);
            } else {
                return $this->showStatementForManagers($user, $month, $year, $landlordId, $_format, $yearNow);
            }
        } catch (\Exception $ex) {
            return new Response($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 
     * @param User $user
     * @param string $month
     * @param string $year
     * @param string $landlordId
     * @param string $_format
     * @param string $yearNow
     * @return Response
     */
    protected function showStatementForManagers(User $user, $month, $year, $landlordId, $_format,
            $yearNow) {
        $this->checkMonthYear($month, $year, $yearNow);
        
        $template = sprintf('ErpUserBundle:Statement:statement-landlord.%s.twig', $_format);
        
        /** @var $user \Erp\UserBundle\Entity\User */
        $landlord = $this->em->getRepository(User::REPOSITORY)->find($landlordId);
        
        $records = $this->get('erp.users.statement.service')
                ->getRecordsForManagerStatement($user, $landlord, $year, $month);
        
        $parameters = array(
            'user' => $user,
            'landlord' => $landlord,
            'period' => (new DateTime)->setDate($year, $month, 1)->format('F Y'),
            'records' => $records,
            'totalIncome' => array_sum(array_column($records, 'income')),
            'totalExpenses' => array_sum(array_column($records, 'expense'))
        );
        
        $routePdfLink = 'erp_user_accounting_statement';
        $baseFileNameToExport = 'zoobdoo_owner-statement';
        
        return $this->generateResponse($_format, $template, $parameters, $routePdfLink,
                $baseFileNameToExport, $month, $year, $landlordId);
    }

    /**
     * 
     * @param string $month
     * @param string $year
     * @param string $_format
     * @param string $yearNow
     * @return Response
     */
    protected function showGenericStatement(User $user, $month, $year, $_format, $yearNow) {
        $this->checkMonthYear($month, $year, $yearNow);

        $template = sprintf('ErpUserBundle:Statement:statement.%s.twig', $_format);

        list($incomes, $expenses) = $this->get('erp.users.statement.service')
                ->getRecordsForStatement($user, $year, $month);

        $parameters = array(
            'user' => $user,
            'period' => ($month == 0) ? ('Year ' . $year) : (new DateTime)->setDate($year, $month, 1)->format('F Y'),
            'incomes' => $incomes,
            'totalIncome' => array_sum($incomes),
            'expenses' => $expenses,
            'totalExpenses' => array_sum($expenses),
        );
        
        $routePdfLink = 'erp_user_accounting_statement';
        $baseFileNameToExport = 'zoobdoo_income-statement';

        return $this->generateResponse($_format, $template, $parameters, $routePdfLink,
                $baseFileNameToExport, $month, $year);
    }

    /**
     * 
     * @param string $month
     * @param string $year
     * @param string $yearNow
     */
    private function checkMonthYear(&$month, &$year, $yearNow) {
        if (($month < 0) || ($month > 12)) {
            $month = 0;
        }

        if ($year > $yearNow) {
            $year = $yearNow;
        }
    }
    
    /**
     * 
     * @param string $_format
     * @param string $template
     * @param string $parameters
     * @param string $route
     * @param string $baseFilename
     * @param string $month
     * @param string $year
     * @param string $landlordId
     * @return Response
     */
    private function generateResponse($_format, $template, $parameters, $route, $baseFilename, $month, $year, $landlordId = '') {
        if ($_format == 'html') {
            $pdf_link = $this->generateUrl($route, array(
                '_format' => 'pdf',
                'erp_form_statement_year' => $year,
                'erp_form_statement_month' => $month,
                'erp_form_statement_landlord' => $landlordId
            ));

            $parameters['pdfLink'] = $pdf_link;

            return $this->render($template, $parameters);
        } elseif ($_format == 'pdf') {
            if ($month == 0) {
                $fileName = sprintf('%s_year%s_%s.pdf', $baseFilename, $year, (new \DateTime())->format('d_m_Y'));
            } else {
                $date = new DateTime();
                $date->setDate($year, $month, 1);
                $fileName = sprintf('%s_year%s_month%s_%s.pdf', $baseFilename, $date->format('Y'), $date->format('F'), (new \DateTime())->format('d_m_Y'));
            }

            $html = $this->renderView($template, $parameters);
            $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html);

            return $this->pdfResponse($pdf, $fileName);
        }
    }

}
