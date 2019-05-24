<?php

namespace Erp\PropertyBundle\DataFixtures\ORM;

use Erp\PropertyBundle\Entity\ContractForm;
use Erp\PropertyBundle\Entity\ContractSection;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ContractFormFixture extends Fixture
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $contractForm = new ContractForm();
        $contractForm->setIsDefault(1);
        $contractForm->setIsPublished(0);
        $contractForm->setProperty(null);
        $contractForm->setCreatedDate();
        $contractForm->setUpdatedDate();
        $manager->persist($contractForm);
        $manager->flush();

        $contractSection = new ContractSection();
        $contractSection->setContractForm($contractForm);
        $contractSection->setSort(10);
        $contractSection->setContent('<p style="text-align: center;"><strong>RESIDENTIAL LEASE AGREEMENT</strong><br />for</p>
<p style="text-align: center;">___________________________________________________<br />(Property Address)</p>
<p style="text-align: justify;"><br /><strong>1. This AGREEMENT</strong> is entered into this ___________ day of ____________________________ , ___________&nbsp;between ____________________________________________________, ("LANDLORD") legal owner of the property through the Owner\'s&nbsp;</p>
<p style="text-align: justify;">BROKER, _______________________________________, ("BROKER") and</p>
<p style="text-align: justify;"><span style="line-height: 1.4285;">Tenant\'s Name: ______________________________________&nbsp;Tenant\'s Name: _________________________________________</span></p>
<p style="text-align: justify;"><span style="line-height: 1.4285;">Tenant\'s Name: ______________________________________&nbsp;Tenant\'s Name: _________________________________________</span></p>
<p style="text-align: justify;">(collectively, "TENANT"), which parties hereby agree to as follows:&nbsp;</p>
<p style="text-align: justify;"><br /><strong>2. SUMMARY:</strong> The initial rents, charges and deposits are as follows:</p>
<table style="width: 898px;" border="0">
<tbody>
<tr style="height: 64px;">
<td style="width: 268px; height: 64px;">&nbsp;</td>
<td style="width: 218px; height: 64px;">Total&nbsp;Amount</td>
<td style="width: 213px; height: 64px;">&nbsp;Received</td>
<td style="width: 198px; height: 64px;">Balance Due
<p>Prior to Occupancy</p>
</td>
</tr>
<tr style="height: 23px;">
<td style="width: 268px; height: 23px;">Rent: From _____, To _____</td>
<td style="width: 218px; height: 23px;">$___________________</td>
<td style="width: 213px; height: 23px;">$&nbsp;&nbsp;___________________</td>
<td style="width: 198px; height: 23px;">$&nbsp;&nbsp;__________________</td>
</tr>
<tr style="height: 23px;">
<td style="width: 268px; height: 23px;">Security Deposit</td>
<td style="width: 218px; height: 23px;">$&nbsp;___________________</td>
<td style="width: 213px; height: 23px;">$&nbsp;&nbsp;___________________</td>
<td style="width: 198px; height: 23px;">$&nbsp;&nbsp;__________________</td>
</tr>
<tr style="height: 23px;">
<td style="width: 268px; height: 23px;">Key Deposit</td>
<td style="width: 218px; height: 23px;">$ ___________________</td>
<td style="width: 213px; height: 23px;">$&nbsp;&nbsp;___________________</td>
<td style="width: 198px; height: 23px;">$&nbsp;&nbsp;__________________</td>
</tr>
<tr style="height: 23px;">
<td style="width: 268px; height: 23px;">Admin Fee/Credit App Fee (Non-refundable)</td>
<td style="width: 218px; height: 23px;">$ ___________________</td>
<td style="width: 213px; height: 23px;">$&nbsp;&nbsp;___________________</td>
<td style="width: 198px; height: 23px;">$&nbsp;&nbsp;__________________</td>
</tr>
<tr style="height: 23px;">
<td style="width: 268px; height: 23px;">Pet Deposit</td>
<td style="width: 218px; height: 23px;">$ ___________________</td>
<td style="width: 213px; height: 23px;">$&nbsp;&nbsp;___________________</td>
<td style="width: 198px; height: 23px;">$&nbsp;&nbsp;__________________</td>
</tr>
<tr style="height: 23px;">
<td style="width: 268px; height: 23px;">Cleaning Deposit</td>
<td style="width: 218px; height: 23px;">$ ___________________</td>
<td style="width: 213px; height: 23px;">$&nbsp;&nbsp;___________________</td>
<td style="width: 198px; height: 23px;">$&nbsp;&nbsp;__________________</td>
</tr>
<tr style="height: 22px;">
<td style="width: 268px; height: 22px;">Last Month\'s Rent Security</td>
<td style="width: 218px; height: 22px;">$ ___________________</td>
<td style="width: 213px; height: 22px;">$&nbsp;&nbsp;___________________</td>
<td style="width: 198px; height: 22px;">$&nbsp;&nbsp;__________________</td>
</tr>
<tr style="height: 23px;">
<td style="width: 268px; height: 23px;">CIC Registration</td>
<td style="width: 218px; height: 23px;">$ ___________________</td>
<td style="width: 213px; height: 23px;">$&nbsp;&nbsp;___________________</td>
<td style="width: 198px; height: 23px;">$&nbsp;&nbsp;__________________</td>
</tr>
<tr style="height: 23px;">
<td style="width: 268px; height: 23px;">Utility Proration</td>
<td style="width: 218px; height: 23px;">$ ___________________</td>
<td style="width: 213px; height: 23px;">$&nbsp;&nbsp;___________________</td>
<td style="width: 198px; height: 23px;">$&nbsp;&nbsp;__________________</td>
</tr>
<tr style="height: 23px;">
<td style="width: 268px; height: 23px;">Sewer/Trash Proration</td>
<td style="width: 218px; height: 23px;">$ ___________________</td>
<td style="width: 213px; height: 23px;">$ &nbsp;___________________</td>
<td style="width: 198px; height: 23px;">$&nbsp;&nbsp;__________________</td>
</tr>
<tr style="height: 23px;">
<td style="width: 268px; height: 23px;">Other</td>
<td style="width: 218px; height: 23px;">$ ___________________</td>
<td style="width: 213px; height: 23px;">$&nbsp;&nbsp;___________________</td>
<td style="width: 198px; height: 23px;">$&nbsp;&nbsp;__________________</td>
</tr>
<tr style="height: 23px;">
<td style="width: 268px; height: 23px;">Other</td>
<td style="width: 218px; height: 23px;">$ ___________________</td>
<td style="width: 213px; height: 23px;">$&nbsp;&nbsp;___________________</td>
<td style="width: 198px; height: 23px;">$&nbsp;&nbsp;__________________</td>
</tr>
<tr style="height: 23px;">
<td style="width: 268px; height: 23px;">Other</td>
<td style="width: 218px; height: 23px;">$ ___________________</td>
<td style="width: 213px; height: 23px;">$&nbsp;&nbsp;___________________</td>
<td style="width: 198px; height: 23px;">$&nbsp;&nbsp;__________________</td>
</tr>
<tr style="height: 23px;">
<td style="width: 268px; height: 23px;">Other</td>
<td style="width: 218px; height: 23px;">$ ___________________</td>
<td style="width: 213px; height: 23px;">$&nbsp;&nbsp;___________________</td>
<td style="width: 198px; height: 23px;">$&nbsp;&nbsp;__________________</td>
</tr>
<tr style="height: 23px;">
<td style="width: 268px; height: 23px;">TOTAL</td>
<td style="width: 218px; height: 23px;">$ ___________________</td>
<td style="width: 213px; height: 23px;">$&nbsp;&nbsp;___________________</td>
<td style="width: 198px; height: 23px;">$&nbsp;&nbsp;__________________</td>
</tr>
</tbody>
</table>
<p style="text-align: justify;"><strong>(Any balance due prior to occupancy to be paid in CERTIFIED FUNDS)</strong><br /><br /><strong>3. ADDITIONAL MONIES DUE:</strong>&nbsp;_______________________________________________________________________________</p>
<p style="text-align: justify;">____________________________________________________________________________________________________________&nbsp;</p>
<p style="text-align: justify;"><strong>4.&nbsp;PREMISES:</strong> Landlord hereby leases to TENANT and TENANT hereby leases from Landlord, subject to the terms and conditions of the lease, the Premises known and designated as ___________________________________________________________________________<br />consisting of ________________________________________________("the Premises").&nbsp;</p>
<p style="text-align: justify;"><strong>5. TERM:</strong> The term hereof shall commence on _____________________________________ and continue until _______________________, for a total rent of ____________________$ , then on a month-to-month basis&nbsp;thereafter, until either party shall terminate the same by giving the other party thirty (30) days written notice&nbsp;delivered by certified mail (all calculation based on 30 day month).</p>
<p style="text-align: left;"><strong>6. RENT:</strong> TENANT shall pay rent at the monthly rate of $ ________________, in advance, on the _______________day&nbsp;of every month beginning the _______________day of _______________________, and delinquent after ____________________.&nbsp;There is no grace period. If rent is delinquent, it must be paid in the form of certified funds.</p>');
        $contractSection->setCreatedDate();
        $contractSection->setUpdatedDate();
        $manager->persist($contractSection);
        $manager->flush();

        $contractSection = new ContractSection();
        $contractSection->setContractForm($contractForm);
        $contractSection->setSort(20);
        $contractSection->setContent('<p style="text-align: justify;"><strong>7. PLACE OF PAYMENTS:</strong> TENANT shall make all payments payable to ______________________________________________<br />&nbsp;and shall mail such payments to: _________________________________________________________________________________<br />_________________________________________ -or- ________________ hand deliver such payments to&nbsp;during normal business hours.&nbsp;</p>
<p style="text-align: justify;"><strong>8. ADDITIONAL FEES:</strong></p>
<p style="text-align: justify;">&nbsp; &nbsp; &nbsp; &nbsp;<strong>A. LATE FEES:</strong> In the event TENANT fails to pay rent when due, TENANT shall pay a late fee of ______________________$</p>
<p style="text-align: justify;">plus $ _________________ per day for each day after _______ days that the sum was due.</p>
<p style="text-align: justify;">&nbsp; &nbsp; <strong>B. DISHONORED CHECKS:</strong> A charge of $ _________________________ shall be imposed for each dishonored&nbsp;check made by TENANT to LANDLORD. TENANT agrees to pay all rents, all late fees, all notice fees and all&nbsp;costs to honor a returned check with certified funds. After TENANT has tendered a check which is dishonored,&nbsp;TENANT hereby agrees to pay all remaining payments including rent due under this Agreement by certified funds.&nbsp;Any payments tendered to LANDLORD thereafter, which are not in the form of certified funds, shall be treated as if&nbsp;TENANT failed to make said payment until certified funds are received. LANDLORD presumes that TENANT is&nbsp;aware of the criminal sanctions and penalties for issuance of a check which TENANT knows is drawn upon&nbsp;insufficient funds and which is tendered for the purpose of committing a fraud upon a creditor.</p>
<p style="text-align: justify;">&nbsp; &nbsp;&nbsp;<strong>C. ADDITIONAL RENT:</strong> All late fees and dishonored check charges shall be due when incurred and shall&nbsp;become additional rent. Payments will be applied to charges which become rent in the order accumulated. All&nbsp;unpaid charges or any fees owed by TENANT, including but not limited to notice fees, attorney\'s fees, repair bills,&nbsp;utility bills, landscape/pool repair and maintenance bills and CIC fines will become additional rent at the beginning&nbsp;of the month after TENANT is billed. TENANT\'S failure to pay the full amount for a period may result in the&nbsp;initiation of eviction proceedings. LANDLORD\'S acceptance of any late fee or dishonored check fee shall not act as&nbsp;a waiver of any default of TENANT, nor as an extension of the date on which rent is due. LANDLORD reserves the&nbsp;right to exercise any other rights and remedies under this Agreement or as provided by law.</p>
<p style="text-align: justify;"><strong>9.&nbsp;SECURITY DEPOSITS:</strong> Upon execution of this Agreement, TENANT shall deposit with LANDLORD as a Security Deposit the sum stated in paragraph 2. TENANT shall not apply the Security Deposit to, or in lieu of, rent. At any time during the term of this Agreement and upon termination of the tenancy by either party for any reason, the LANDLORD may claim, from the Security Deposit, such amounts due Landlord under this Agreement. Any termination prior to the initial term set forth in paragraph 5, or failure of TENANT to provide proper notice of termination, is a default in the payment of rent for the remainder of the lease term, which may be offset by the Security Deposit. Pursuant to NRS 118A.242, LANDLORD shall provide TENANT with a written, itemized accounting of the disposition of the Security Deposit within thirty (30) days of termination. TENANT agrees, upon termination of the tenancy, to provide LANDLORD with a forwarding address to prevent a delay in receiving the&nbsp;accounting and any refund.</p>
<p style="text-align: justify;"><strong>10. TRUST ACCOUNTS:</strong> BROKER shall retain all interest earned, if any, on security deposits to offset&nbsp;administration and bookkeeping fees.</p>
<p style="text-align: justify;"><strong>11. EVICTION COSTS:</strong> TENANT shall be charged an administrative fee of $ ______________________&nbsp;per eviction&nbsp;attempt to offset the costs of eviction notices and proceedings. TENANT may be charged for service of legal&nbsp;notices and all related fees according to actual costs incurred.</p>
<p style="text-align: justify;"><strong>12. CARDS AND KEYS:</strong> Upon execution of the Agreement, TENANT shall receive the following:</p>
<table style="width: 850px;">
<tbody>
<tr style="height: 23px;">
<td style="height: 23px;">_______</td>
<td style="height: 23px;">Door key(s)</td>
<td style="height: 23px;">_______</td>
<td style="height: 23px;">Garage Transmitter(s)</td>
<td style="height: 23px;">_______</td>
<td style="height: 23px;">Other(s)</td>
<td style="height: 23px;">_______</td>
</tr>
<tr style="height: 23px;">
<td style="height: 23px;">_______</td>
<td style="height: 23px;">Mailbox key(s)</td>
<td style="height: 23px;">_______</td>
<td style="height: 23px;">Gate Card(s)</td>
<td style="height: 23px;">_______</td>
<td style="height: 23px;">Other(s)</td>
<td style="height: 23px;">_______</td>
</tr>
<tr style="height: 23px;">
<td style="height: 23px;">_______</td>
<td style="height: 23px;">Laundry Room key(s)</td>
<td style="height: 23px;">_______</td>
<td style="height: 23px;">Gate Transmitter(s)</td>
<td style="height: 23px;">_______</td>
<td style="height: 23px;">Other(s)</td>
<td style="height: 23px;">_______</td>
</tr>
</tbody>
</table>
<p style="text-align: justify;"><br />Tenant shall make a key deposit (if any) in the amount set forth in paragraph 2 upon execution of this Agreement.&nbsp;The key deposit shall be refunded within 30 days of Tenant\'s return of all cards and/or keys to Landlord or&nbsp;Landlord\'s BROKER.</p>');
        $contractSection->setCreatedDate();
        $contractSection->setUpdatedDate();
        $manager->persist($contractSection);
        $manager->flush();

        $contractSection = new ContractSection();
        $contractSection->setContractForm($contractForm);
        $contractSection->setSort(30);
        $contractSection->setContent('<p style="text-align: justify;"><strong>13. CONVEYANCES AND USES:</strong> TENANT shall not assign, sublet or transfer TENANT\'S interest, nor any part&nbsp;thereof, without prior written consent of LANDLORD. TENANT shall use the Premises for residential purposes&nbsp;only and not for any commercial enterprise or for any purpose which is illegal. TENANT shall not commit waste,&nbsp;cause excessive noise, create a nuisance or disturb others.</p>
<p style="text-align: justify;"><strong>14. OCCUPANTS:</strong> Occupants of the Premises shall be limited to __________________ persons and shall be used solely for&nbsp;housing accommodations and for no other purpose. TENANT represents that the following person(s) will live in the&nbsp;Premises: ___________________________________________________________________________________________________________________</p>
<p style="text-align: justify;">___________________________________________________________________________________________________________________<br /><br /><strong>15. GUESTS:</strong> The TENANT agrees to pay the sum of $ ___________________________ per day for each guest remaining on&nbsp;the Premises more than ____________ days. Notwithstanding the foregoing, in no event shall any guest remain on the&nbsp;Premises for more than ______________ days.</p>
<p style="text-align: justify;"><strong>16. UTILITIES:</strong> LESSEE shall immediately connect all utilities and services of premises upon commencement of&nbsp;lease. LESSEE is to pay when due all utilities and other charges in connection with LESSEE\'s individual rented&nbsp;premises. Responsibility is described as (T) for Tenant and (O) for Owner:</p>
<table style="width: 850px; height: 100px;">
<tbody>
<tr style="height: 33px;">
<td style="height: 33px;">Electricity</td>
<td style="height: 33px;">__________ &nbsp; &nbsp;</td>
<td style="height: 33px;">Trash&nbsp;</td>
<td style="height: 33px;">__________ &nbsp; &nbsp;</td>
<td style="height: 33px;">Phone</td>
<td style="height: 33px;">__________ &nbsp; &nbsp;</td>
<td style="height: 33px;">Other</td>
<td style="height: 33px;">__________ &nbsp;&nbsp;&nbsp;&nbsp;</td>
</tr>
<tr style="height: 33px;">
<td style="height: 33px;">Gas</td>
<td style="height: 33px;">__________ &nbsp; &nbsp;</td>
<td style="height: 33px;">Sewer</td>
<td style="height: 33px;">__________ &nbsp; &nbsp;</td>
<td style="height: 33px;">Cable</td>
<td style="height: 33px;">__________ &nbsp; &nbsp;</td>
<td style="height: 33px;">Other</td>
<td style="height: 33px;">__________ &nbsp; &nbsp;</td>
</tr>
<tr style="height: 33px;">
<td style="height: 33px;">Water</td>
<td style="height: 33px;">__________ &nbsp; &nbsp;</td>
<td style="height: 33px;">Septic</td>
<td style="height: 33px;">__________ &nbsp; &nbsp;</td>
<td style="height: 33px;">Association Fees</td>
<td style="height: 33px;">__________ &nbsp; &nbsp;</td>
<td style="height: 33px;">&nbsp;</td>
<td style="height: 33px;">&nbsp;</td>
</tr>
</tbody>
</table>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">a. TENANT is responsible to connect the following utilities in TENANT\'S name: __________________________________________</p>
<p style="text-align: justify;">_______________________________________________________________________________________________________________<br />b. LANDLORD will maintain the connection of the following utilities in LANDLORD\'s name and bill&nbsp;TENANT for connection fees and use accordingly: _________________________________________________________________________________________________</p>
<p style="text-align: justify;">_______________________________________________________________________________________________________________<br />c.&nbsp;No additional phone or cable lines or outlets shall be obtained for the Premises without the&nbsp;LANDLORD\'s written consent. In the event of LANDLORD\'s consent, TENANT shall be responsible for all&nbsp;costs associated with the additional lines or outlets.<br />d. If an alarm system exists on the Premises, TENANT shall obtain the services of an alarm services&nbsp;company and shall pay all costs associated therewith.<br />e. Other: ______________________________________________________________________________________________________</p>
<p style="text-align: justify;">________________________________________________________________________________________________________________<br /><br /><strong>17. PEST NOTICE:</strong> <em>TENANT understands that various pest, rodent and insect species (collectively, "pests") exist in Southern Nevada. Pests may include, but are not limited to, scorpions (approximately 23 species, including bark&nbsp;scorpions), spiders (including black widow and brown recluse), bees, snakes, ants, termites, rats, mice and pigeons.&nbsp;The existence of pests may vary by season and location. Within thirty (30) days of occupancy, if the Premises has&nbsp;pests, LANDLORD, at TENANT\'s request, will arrange for and pay for the initial pest control spraying. TENANT&nbsp;agrees to pay for the monthly pest control spraying fees. The names and numbers of pest control providers are in the&nbsp;yellow pages under "PEST." For more information on pests and pest control providers, TENANT should contact the&nbsp;State of Nevada Division of Agriculture at www.agri.nv.gov.</em></p>
<p style="text-align: justify;"><strong>18. PETS:</strong> No pet shall be on or about the Premises at any time without written permission of LANDLORD. In the&nbsp;event TENANT wishes to have a pet, TENANT will complete an Application for Pet Approval. Should written&nbsp;permission be granted for occupancy of the designated pet, an additional security deposit in the amount of $ _____________&nbsp;will be required and paid by TENANT in advance subject to deposit terms and conditions aforementioned. In the&nbsp;event written permission shall be granted, TENANT shall be required to procure and provide to Landlord written&nbsp;evidence that TENANT has obtained such insurance as may be available against property damage to the Premises and&nbsp;liability to third party injury. Each such policy shall name LANDLORD and LANDLORD\'S AGENT as additional&nbsp;insureds. A copy of each such policy shall be provided to Landlord or Landlord\'s BROKER prior to any pets being&nbsp;allowed within the Premises. If TENANT obtains a pet without written permission of LANDLORD, TENANT agrees&nbsp;to pay an immediate fine of $500. TENANT agrees to indemnify LANDLORD for any and all liability, loss and&nbsp;damages which LANDLORD may suffer as a result of any animal in the Premises, whether or not written&nbsp;permission was granted.&nbsp;</p>');
        $contractSection->setCreatedDate();
        $contractSection->setUpdatedDate();
        $manager->persist($contractSection);
        $manager->flush();

        $contractSection = new ContractSection();
        $contractSection->setContractForm($contractForm);
        $contractSection->setSort(40);
        $contractSection->setContent('<p style="text-align: justify;"><strong>19. RESTRICTIONS:</strong> TENANT shall not keep or permit to be kept in, on, or about the Premises: waterbeds, boats,&nbsp;campers, trailers, mobile homes, recreational or commercial vehicles or any non-operative vehicles except as&nbsp;follows: ___________________________________ ________________________________________________________________________________________________________________.<br /><strong>TENANT shall not conduct nor permit any work on vehicles on the premises.</strong></p>
<p style="text-align: justify;"><strong>20. ALTERATIONS:</strong> TENANT shall make no alterations to the Premises without LANDLORD\'s written consent. All&nbsp;alterations or improvements made to the Premises, shall, unless otherwise provided by written agreement between&nbsp;parties hereto, become the property of LANDLORD and shall remain upon the Premises and shall constitute a&nbsp;fixture permanently affixed to the Premises. In the event of any alterations, TENANT shall be responsible for&nbsp;restoring the Premises to its original condition if requested by LANDLORD or LANDLORD\'s BROKER.&nbsp;</p>
<p style="text-align: justify;"><strong>21. DEFAULT:</strong> Failure by TENANT to pay rent, perform any obligation under this Agreement, or comply with any&nbsp;Association Governing Documents (if any), or TENANT\'s engagement in activity prohibited by this Agreement, or&nbsp;TENANT\'s failure to comply with any and all applicable laws, shall be considered a default hereunder. Upon&nbsp;default, LANDLORD may, at its option, terminate this tenancy upon giving proper notice. Upon default,&nbsp;LANDLORD shall issue a proper itemized statement to TENANT noting the amount owed by TENANT.<br />LANDLORD may pursue any and all legal and equitable remedies available.</p>
<p style="text-align: justify;"><strong>22. ENFORCEMENT:</strong> Any failure by LANDLORD to enforce the terms of this Agreement shall not constitute a&nbsp;waiver of said terms by LANDLORD. Acceptance of rent due by LANDLORD after any default shall not be&nbsp;construed to waive any right of LANDLORD or affect any notice of termination or eviction.</p>
<p style="text-align: justify;"><strong>23. NOTICE OF INTENT TO VACATE:</strong> TENANT shall provide notice of TENANT\'s intention to vacate the&nbsp;Premises at the expiration of this Agreement. <strong>Such notice shall be in writing and shall be provided to&nbsp;LANDLORD prior to the first day of the last month of the lease term set forth in section 5 of this Agreement.&nbsp;In no event shall notice be less than 30 days prior to the expiration of the term of this Agreement.</strong> In the event&nbsp;TENANT fails to provide such notice, TENANT shall be deemed to be holding-over on a month-to-month basis&nbsp;until 30 days after such notice. During a holdover not authorized by LANDLORD, rent shall increase by ___________%.</p>
<p style="text-align: justify;"><strong>24. TERMINATION:</strong> Upon termination of the tenancy, TENANT shall surrender and vacate the Premises and shall&nbsp;remove any and all of TENANT\'S property. TENANT shall return keys, personal property and Premises to the&nbsp;LANDLORD in good, clean and sanitary condition, normal wear excepted. TENANT will allow LANDLORD to&nbsp;inspect the Premises in the TENANT\'s presence to verify the condition of the Premises.</p>
<p style="text-align: justify;"><strong>25. EMERGENCIES:</strong> The name, address and phone number of the party who will handle maintenance or essential&nbsp;services emergencies on behalf of the LANDLORD is as follows: ________________________________________________________________________________</p>
<p style="text-align: justify;">___________________________________________________________________________________________________________________</p>
<p style="text-align: justify;"><strong>26. MAINTENANCE:</strong> TENANT shall keep the Premises in a clean and good condition. TENANT shall immediately&nbsp;report to the LANDLORD any defect or problem pertaining to plumbing, wiring or workmanship on the Premises.&nbsp;TENANT agrees to notify LANDLORD of any water leakage and/or damage within 24 hours of the occurrence.&nbsp;TENANT understands that TENANT may be held responsible for any water and/or mold damage, including the&nbsp;costs of remediation of such damage. TENANT shall be responsible for any MINOR repairs necessary to the&nbsp;Premises up to and including the cost of $ _________________________ . TENANT agrees to pay for all repairs,&nbsp;replacements and maintenance required by TENANT\'s misconduct or negligence or that of TENANT\'s family, pets,&nbsp;licensees and guests, including but not limited to any damage done by wind or rain caused by leaving windows&nbsp;open and/or by overflow of water, or stoppage of waste pipes, or any other damage to appliances, carpeting or the&nbsp;building in general. At LANDLORD\'s option, such charges shall be paid immediately or be regarded as additional&nbsp;rent to be paid no later than the next monthly payment date following such repairs.</p>');
        $contractSection->setCreatedDate();
        $contractSection->setUpdatedDate();
        $manager->persist($contractSection);
        $manager->flush();

        $contractSection = new ContractSection();
        $contractSection->setContractForm($contractForm);
        $contractSection->setSort(50);
        $contractSection->setContent('<p style="text-align: justify;">a. TENANT shall change filters in the heating and air conditioning systems at least once every month, at&nbsp;TENANT\'s own expense. LANDLORD shall maintain the heating and air conditioning systems and provide for&nbsp;major repairs. However, any repairs to the heating or cooling system caused by dirty filters due to TENANT&nbsp;neglect will be the responsibility of TENANT.</p>
<p style="text-align: justify;">b. TENANT shall replace all broken glass, regardless of cause of damage, at TENANT\'s expense.</p>
<p style="text-align: justify;">c. In the case of landscaping and/or a swimming pool being maintained by a contractor, TENANT agrees to&nbsp;cooperate with the landscape and/or pool contractor in a satisfactory manner. LANDLORD provided landscaping&nbsp;maintenance is not to be construed as a waiver of any responsibility of the TENANT to keep and maintain the&nbsp;landscaping and/or shrubs, trees and sprinkler system in good condition. In the event the landscaping is not being&nbsp;maintained by a Contractor, TENANT shall maintain lawns, shrubs and trees. TENANT shall water all lawns,&nbsp;shrubs and trees, mow the lawns on a regular basis, trim the trees and fertilize lawns, shrubs and trees. If&nbsp;TENANT fails to maintain the landscaping in a satisfactory manner, LANDLORD may have the landscaping&nbsp;maintained by a landscaping contractor and charge TENANT with the actual cost. Said costs shall immediately&nbsp;become additional rent.</p>
<p style="text-align: justify;">d. LANDLORD shall be responsible for all major electrical problems that are not caused by TENANT.</p>
<p style="text-align: justify;">e. TENANT _________<strong>shall -OR</strong>- _________<strong>shall not</strong> have carpets professionally cleaned upon move out. If cleaned,&nbsp;TENANT shall present LANDLORD or LANDLORD\'s BROKER with a receipt from a reputable carpet cleaning&nbsp;company.</p>
<p style="text-align: justify;">f. There<strong>&nbsp;_________is -OR-_________ is not</strong> a pool contractor whose name and phone number are as follows:___________________________</p>
<p style="text-align: justify;">___________________________________________________________________________________________________________________.<br />If there is no such contractor, TENANT agrees to maintain the pool, if any. TENANT agrees to maintain the&nbsp;water level, sweep, clean and keep in good condition. If TENANT fails to maintain the pool in a satisfactory&nbsp;manner, LANDLORD may have the pool maintained by a licensed pool service and charge TENANT with the&nbsp;actual cost. Said costs shall become additional rent.</p>
<p style="text-align: justify;"><strong>27. ACCESS:</strong> TENANT agrees to grant LANDLORD the right to enter the Premises at all reasonable times and for all&nbsp;reasonable purposes including showing to prospective lessees, buyers, appraisers or insurance agents or other&nbsp;business therein as requested by LANDLORD, and for BROKER\'s periodic maintenance reviews. If TENANT fails&nbsp;to keep scheduled appointments with vendors to make necessary/required repairs, TENANT shall pay for any&nbsp;additional charges incurred which will then become part of the next month\'s rent and be considered additional rent.<br />TENANT shall not deny LANDLORD his/her rights of reasonable entry to the Premises. LANDLORD shall have&nbsp;the right to enter in case of emergency and other situations as specifically allowed by law. LANDLORD agrees to&nbsp;give TENANT twenty-four (24) hours notification for entry, except in case of emergency.</p>
<p style="text-align: justify;"><strong>28. INVENTORY:</strong> It is agreed that the following inventory is now on said premises. (Check if present; cross out if&nbsp;absent.)</p>
<table style="width: 850px; height: 250px;">
<tbody>
<tr style="height: 35px;">
<td style="height: 35px;">_______</td>
<td style="height: 35px;">Refrigerator</td>
<td style="height: 35px;">_______</td>
<td style="height: 35px;">Intercom System</td>
<td style="height: 35px;">_______</td>
<td style="height: 35px;">Spa Equipment</td>
<td style="height: 35px;">_______</td>
</tr>
<tr style="height: 35px;">
<td style="height: 35px;">_______</td>
<td style="height: 35px;">Stove</td>
<td style="height: 35px;">_______</td>
<td style="height: 35px;">Alarm System</td>
<td style="height: 35px;">_______</td>
<td style="height: 35px;">Auto Sprinklers</td>
<td style="height: 35px;">_______</td>
</tr>
<tr style="height: 35px;">
<td style="height: 35px;">_______</td>
<td style="height: 35px;">Microwave</td>
<td style="height: 35px;">_______</td>
<td style="height: 35px;">Trash Compactor</td>
<td style="height: 35px;">_______</td>
<td style="height: 35px;">Auto Garage Openers</td>
<td style="height: 35px;">_______</td>
</tr>
<tr style="height: 36px;">
<td style="height: 36px;">_______</td>
<td style="height: 36px;">Disposal</td>
<td style="height: 36px;">_______</td>
<td style="height: 36px;">Ceiling Fans</td>
<td style="height: 36px;">_______</td>
<td style="height: 36px;">BBQ</td>
<td style="height: 36px;">_______</td>
</tr>
<tr style="height: 36px;">
<td style="height: 36px;">_______</td>
<td style="height: 36px;">Dishwasher</td>
<td style="height: 36px;">_______</td>
<td style="height: 36px;">Water Conditioner Equip.</td>
<td style="height: 36px;">_______</td>
<td style="height: 36px;">Solar Screens</td>
<td style="height: 36px;">_______</td>
</tr>
<tr style="height: 36px;">
<td style="height: 36px;">_______</td>
<td style="height: 36px;">Washer</td>
<td style="height: 36px;">_______</td>
<td style="height: 36px;">Floor Coverings</td>
<td style="height: 36px;">_______</td>
<td style="height: 36px;">Pool Equipment</td>
<td style="height: 36px;">_______</td>
</tr>
<tr style="height: 36px;">
<td style="height: 36px;">_______</td>
<td style="height: 36px;">Dryer</td>
<td style="height: 36px;">_______</td>
<td style="height: 36px;">Window Coverings</td>
<td style="height: 36px;">_______</td>
<td style="height: 36px;">Other</td>
<td style="height: 36px;">_______</td>
</tr>
</tbody>
</table>
<p style="text-align: justify;"><br />&nbsp;&nbsp;TENANT assumes responsibility for the care and maintenance thereof.&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;</p>');
        $contractSection->setCreatedDate();
        $contractSection->setUpdatedDate();
        $manager->persist($contractSection);
        $manager->flush();

        $contractSection = new ContractSection();
        $contractSection->setContractForm($contractForm);
        $contractSection->setSort(60);
        $contractSection->setContent('<p style="text-align: justify;"><strong>29. ASSOCIATIONS:</strong> Should the Premises described herein be a part of a common interest community, homeowners&nbsp;association planned unit development, condominium development ("the Association") or such, TENANT hereby&nbsp;agrees to abide by the Governing Documents (INCLUDING Declarations, Bylaws, Articles, Rules and Regulations)&nbsp;of such project and further agrees to be responsible for any fines or penalties levied as a result of failure to do so by&nbsp;himself, his family, licensees or guests. Noncompliance with the Governing Documents shall constitute a violation&nbsp;of this Agreement. Unless billed directly to TENANT by the Association, such fines shall be considered as an&nbsp;addition to rent and shall be due along with the next monthly payment of rent. By initialing this paragraph,&nbsp;TENANT acknowledges receipt of a copy of the applicable Governing Documents. LANDLORD, at LANDLORD\'s&nbsp;expense, shall provide TENANT with any additions to such Governing Documents as they become available.&nbsp;LANDLORD may, at its option, with 30 days notice to TENANT, adopt additional reasonable rules and regulations&nbsp;governing use of the Premises and of the common areas (if any). [ ______ ] [______ ] [______ ] [ ______ ]&nbsp;</p>
<p style="text-align: justify;">&nbsp;<strong>30. INSURANCE:</strong> TENANT <strong>is -OR- is not</strong> required to purchase renter\'s insurance. LANDLORD and BROKER&nbsp;shall be named as additional interests on any such policy. LANDLORD shall not be liable for any damage or&nbsp;injury to TENANT, or any other person, to any property occurring on the Premises or any part thereof, or in&nbsp;common areas thereof. TENANT agrees to indemnify, defend and hold LANDLORD harmless from any claims for&nbsp;damages. TENANT understands that LANDLORD\'s insurance does not cover TENANT\'s personal property. Even<br />if it is not a requirement of this Agreement, TENANT understands that LANDLORD highly recommends that&nbsp;TENANT purchase renter\'s insurance.</p>
<p style="text-align: justify;"><strong>31. ILLEGAL ACTIVITIES PROHIBITED:</strong> TENANT is aware of the following: It is a misdemeanor to commit or&nbsp;maintain a public nuisance as defined in NRS 202.450 or to allow any building or boat to be used for a public&nbsp;nuisance. Any person, who willfully refuses to remove such a nuisance when there is a legal duty to do so, is guilty&nbsp;of a misdemeanor. A public nuisance may be reported to the local sheriff\'s department. A violation of building,&nbsp;health or safety codes or regulations may be reported to the government entity in our local area such as the code&nbsp;enforcement division of the county/city government or the local health or building departments.</p>
<p style="text-align: justify;"><strong>32. ADDITIONAL RESPONSIBILITIES:</strong><br />a. TENANT may install or replace screens at TENANT\'s own expense. Solar screen installation requires written&nbsp;permission from LANDLORD. LANDLORD is not responsible for maintaining screens.</p>
<p style="text-align: justify;">b. With the exception of electric cooking devices, outdoor cooking with portable barbecuing equipment is&nbsp;prohibited within ten (10) feet of any overhang, balcony or opening, unless the Premises is a detached single&nbsp;family home. The storage and/or use of any barbecuing equipment is prohibited indoors, above the first floor and&nbsp;within five (5) feet of any exterior building wall. Adult supervision is required at all times the barbecue&nbsp;equipment is generating heat.</p>
<p style="text-align: justify;">c. The Premises &nbsp;____&nbsp;<strong>have -OR- ____have not</strong> been freshly painted. If not freshly painted, the Premises ____<strong>have -OR- _____have not</strong> been touched up. TENANT will be responsible for the costs for any holes or&nbsp;excessive dirt or smudges that will require repainting.</p>
<p style="text-align: justify;">d. TENANT agrees to coordinate transfer of utilities to LANDLORD or BROKER no less than ____________________________&nbsp;business days of vacating the Premises.</p>
<p style="text-align: justify;">e. Locks may be replaced or re-keyed at the TENANT\'S expense provided TENANT informs LANDLORD and&nbsp;provides LANDLORD with a workable key for each new or changed lock.</p>
<p style="text-align: justify;">f. TENANT may conduct a risk assessment or inspection of the Premise for the presence of lead-based paint&nbsp;and/or lead-based paint hazards at the TENANT\'s expense for a period of ten days after execution of this&nbsp;agreement. Such assessment or inspection shall be conducted by a certified lead-based paint professional. If&nbsp;TENANT for any reason fails to conduct such an assessment or inspection, then TENANT shall be deemed to&nbsp;have elected to lease the Premises "as is" and to have waived this contingency. If TENANT conducts such an&nbsp;assessment or inspection and determines that lead-based paint deficiencies and/or hazards exist, TENANT will&nbsp;notify LANDLORD in writing and provide a copy of the assessment/inspection report. LANDLORD will then&nbsp;have ten days to elect to correct such deficiencies and/or hazards or to terminate this agreement. In the event of&nbsp;termination under this paragraph, the security deposit will be refunded to TENANT. (If the property was&nbsp;constructed prior to 1978, refer to the attached Lead-Based Paint Disclosure.)</p>');
        $contractSection->setCreatedDate();
        $contractSection->setUpdatedDate();
        $manager->persist($contractSection);
        $manager->flush();

        $contractSection = new ContractSection();
        $contractSection->setContractForm($contractForm);
        $contractSection->setSort(70);
        $contractSection->setContent('<p style="text-align: justify;">g. TENANT may display the flag of the United States, made of cloth, fabric or paper, from a pole, staff or in a&nbsp;window, and in accordance with 4 USC Chapter 1. LANDLORD may, at its option, with 30 days notice to&nbsp;TENANT, adopt additional reasonable rules and regulations governing the display of the flag of the United States.</p>
<p style="text-align: justify;">h. TENANT may display political signs subject to any applicable provisions of law governing the posting of&nbsp;political signs, and, if the Premises are located within a CIC, the provisions of NRS 116 and any governing&nbsp;documents related to the posting of political signs. All political signs exhibited must not be larger than 24 inches&nbsp;by 36 inches. LANDLORD may not exhibit any political sign on the Premises unless the tenant consents, in&nbsp;writing, to the exhibition of the political sign. TENANT may exhibit as many political signs as desired, but may&nbsp;not exhibit more than one political sign for each candidate, political party or ballot question.</p>
<p style="text-align: justify;"><strong>33. CHANGES MUST BE IN WRITING:</strong> No changes, modifications or amendment of this Agreement shall be valid&nbsp;or binding unless such changes, modifications or amendment are in writing and signed by each party. Such changes&nbsp;shall take effect after thirty days notice to TENANT.</p>
<p style="text-align: justify;"><strong>34. CONFLICTS BETWEEN LEASE AND ADDENDUM:</strong> In case of conflict between the provisions of an&nbsp;addendum and any other provisions of this Agreement, the provisions of the addendum shall govern.</p>
<p style="text-align: justify;"><strong>35. ATTORNEY\'S FEES:</strong> In the event of any court action, the prevailing party shall be entitled to be awarded against&nbsp;the losing party all costs and expenses incurred thereby, including, but not limited to, reasonable attorney\'s fees and&nbsp;costs.</p>
<p style="text-align: justify;"><strong>36. NEVADA LAW GOVERNS:</strong> This Agreement is executed and intended to be performed in the State of Nevada in&nbsp;the county where the Premises are located and the laws of the State of Nevada shall govern its interpretation and&nbsp;effect.</p>
<p style="text-align: justify;"><strong>37. WAIVER:</strong> Nothing contained in this Agreement shall be construed as waiving any of the LANDLORD\'s or&nbsp;TENANT\'s rights under the laws of the State of Nevada.</p>
<p style="text-align: justify;"><strong>38. PARTIAL INVALIDITY:</strong> In the event that any provision of this Agreement shall be held invalid or&nbsp;unenforceable, such ruling shall not affect in any respect whatsoever the validity or enforceability of the remainder&nbsp;of this Agreement.</p>
<p style="text-align: justify;"><strong>39. VIOLATIONS OF PROVISIONS:</strong> A single violation by TENANT of any of the provisions of this Agreement&nbsp;shall be deemed a material breach and shall be cause for termination of this Agreement. Unless otherwise provided&nbsp;by the law, proof of any violation of this Agreement shall not require criminal conviction but shall be by a&nbsp;preponderance of the evidence.</p>
<p style="text-align: justify;"><strong>40. SIGNATURES:</strong> The Agreement is accepted and agreed to jointly and severally. The undersigned have read this&nbsp;Agreement and understand and agree to all provisions thereof and further acknowledge that they have received a&nbsp;copy of this Agreement.</p>
<p style="text-align: justify;"><strong>41. LICENSEE DISCLOSURE OF INTEREST:</strong> Pursuant to NAC 645.640, __________________________________________________<br />is a licensed real estate agent in the State(s) of ________________________________________, and has the following interest, direct&nbsp;or indirect, in this transaction: [] Principal (LANDLORD or TENANT) <strong>-OR-</strong>&nbsp;[] family relationship or business<br />interest: _______________________________________________________.</p>');
        $contractSection->setCreatedDate();
        $contractSection->setUpdatedDate();
        $manager->persist($contractSection);
        $manager->flush();

        $contractSection = new ContractSection();
        $contractSection->setContractForm($contractForm);
        $contractSection->setSort(80);
        $contractSection->setContent('<p><strong>42. CONFIRMATION OF REPRESENTATION:</strong> The Agents in this transaction are:<br /><br />Tenant\'s Broker: _______________________________Agent\'s Name: _________________________________________________________&nbsp;<br />Address: __________________________________________________________________________________________________________<br />Phone: ____________________ Fax:____________________ Email:__________________________________________________________&nbsp;<br />License # ______________________________</p>
<p>&nbsp;</p>
<p>Landlord\'s Broker: _______________________________Agent\'s Name: _______________________________________________________&nbsp;<br />Address: __________________________________________________________________________________________________________<br />Phone: ____________________ Fax:____________________ Email:__________________________________________________________&nbsp;<br />License # ______________________________</p>
<p><br /><strong>43. NOTICES:</strong> Unless otherwise required by law, any notice to be given or served upon any party hereto in connection&nbsp;with this Agreement must be in writing and mailed by certificate of mailing to the following addresses:<br /><br />BROKER: _________________________________________________________________________________________________________<br />Address: __________________________________________________________________________________________________________<br />Phone: ____________________ Fax:____________________ Email:__________________________________________________________&nbsp;</p>
<p>TENANT: _________________________________________________________________________________________________________<br />Address: __________________________________________________________________________________________________________<br />Phone: ____________________ Fax:____________________ Email:__________________________________________________________&nbsp;</p>
<p>&nbsp;</p>
<p><strong>44. ADDENDA ATTACHED:</strong> Incorporated into this Agreement are the following addenda, exhibits and other&nbsp;information:<br />A. [] Lease Addendum for Drug Free Housing<br />B. [] Smoke Detector Agreement<br />C. [] Other: ___________________________<br />D. [] Other: ___________________________<br />E. [] Other: ___________________________</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;"><br /><strong>&nbsp;[This space is intentionally blank.]</strong><br /><br /></p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: center;">&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>');
        $contractSection->setCreatedDate();
        $contractSection->setUpdatedDate();
        $manager->persist($contractSection);
        $manager->flush();

        $contractSection = new ContractSection();
        $contractSection->setContractForm($contractForm);
        $contractSection->setSort(90);
        $contractSection->setContent('<p><strong>45. ADDITIONAL TERMS AND CONDITIONS:</strong></p>
<table style="width: 900px; height: 200px;">
<tbody>
<tr style="height: 23px;">
<td style="height: 23px;">______________________________________________________________________________________________________________</td>
</tr>
<tr style="height: 23px;">
<td style="height: 23px;">______________________________________________________________________________________________________________</td>
</tr>
<tr style="height: 23px;">
<td style="height: 23px;">______________________________________________________________________________________________________________</td>
</tr>
<tr style="height: 23px;">
<td style="height: 23px;">______________________________________________________________________________________________________________</td>
</tr>
<tr style="height: 23px;">
<td style="height: 23px;">______________________________________________________________________________________________________________</td>
</tr>
<tr style="height: 23px;">
<td style="height: 23px;">______________________________________________________________________________________________________________</td>
</tr>
<tr style="height: 23px;">
<td style="height: 23px;">______________________________________________________________________________________________________________</td>
</tr>
<tr style="height: 23px;">
<td style="height: 23px;">______________________________________________________________________________________________________________</td>
</tr>
<tr style="height: 23px;">
<td style="height: 23px;">______________________________________________________________________________________________________________</td>
</tr>
<tr style="height: 23px;">
<td style="height: 23px;">______________________________________________________________________________________________________________</td>
</tr>
<tr style="height: 23px;">
<td style="height: 23px;">______________________________________________________________________________________________________________</td>
</tr>
<tr style="height: 23px;">
<td style="height: 23px;">______________________________________________________________________________________________________________</td>
</tr>
<tr style="height: 23px;">
<td style="height: 23px;">______________________________________________________________________________________________________________</td>
</tr>
<tr style="height: 23px;">
<td style="height: 23px;">______________________________________________________________________________________________________________</td>
</tr>
<tr style="height: 23px;">
<td style="height: 23px;">______________________________________________________________________________________________________________</td>
</tr>
</tbody>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p style="text-align: left;">&nbsp;</p>
<table style="width: 893px; height: 250px;">
<tbody>
<tr style="height: 24px;">
<td style="width: 447px; height: 24px;">_____________________________________________________</td>
<td style="width: 445px; height: 24px;">_____________________________________________________</td>
</tr>
<tr style="height: 25px;">
<td style="width: 447px; height: 25px;">LANDLORD/OWNER OF RECORD NAME</td>
<td style="width: 445px; height: 25px;">TENANT\'S SIGNATURE &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;DATE</td>
</tr>
<tr style="height: 25px;">
<td style="width: 447px; height: 25px;">&nbsp;</td>
<td style="width: 445px; height: 25px;">Print Name: _________________________________________</td>
</tr>
<tr style="height: 25px;">
<td style="width: 447px; height: 25px;">&nbsp;</td>
<td style="width: 445px; height: 25px;">Phone: ______________________________________________</td>
</tr>
<tr style="height: 25px;">
<td style="width: 447px; height: 25px;">&nbsp;</td>
<td style="width: 445px; height: 25px;">&nbsp;</td>
</tr>
<tr style="height: 25px;">
<td style="width: 447px; height: 25px;">_____________________________________________________</td>
<td style="width: 445px; height: 25px;">_____________________________________________________</td>
</tr>
<tr style="height: 25px;">
<td style="width: 447px; height: 25px;">MANAGEMENT COMPANY (BROKER) NAME</td>
<td style="width: 445px; height: 25px;">TENANT\'S SIGNATURE &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;DATE</td>
</tr>
<tr style="height: 25px;">
<td style="width: 447px; height: 25px;">&nbsp;</td>
<td style="width: 445px; height: 25px;">Print Name: _________________________________________</td>
</tr>
<tr style="height: 25px;">
<td style="width: 447px; height: 25px;">&nbsp;</td>
<td style="width: 445px; height: 25px;">Phone: ______________________________________________</td>
</tr>
<tr style="height: 25px;">
<td style="width: 447px; height: 25px;">&nbsp;</td>
<td style="width: 445px; height: 25px;">&nbsp;</td>
</tr>
<tr style="height: 25px;">
<td style="width: 447px; height: 25px;">&nbsp;By __________________________________________________</td>
<td style="width: 445px; height: 25px;">&nbsp;_____________________________________________________</td>
</tr>
<tr style="height: 25px;">
<td style="width: 447px; height: 25px;">&nbsp;Authorized AGENT for BROKER SIGNATURE &nbsp; &nbsp; &nbsp; DATE</td>
<td style="width: 445px; height: 25px;">&nbsp;TENANT\'S SIGNATURE &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;DATE</td>
</tr>
<tr style="height: 25px;">
<td style="width: 447px; height: 25px;">&nbsp;</td>
<td style="width: 445px; height: 25px;">&nbsp;Print Name: _________________________________________</td>
</tr>
<tr style="height: 25px;">
<td style="width: 447px; height: 25px;">&nbsp;</td>
<td style="width: 445px; height: 25px;">&nbsp;Phone: ______________________________________________</td>
</tr>
</tbody>
</table>
<p style="text-align: left;">&nbsp;</p>');
        $contractSection->setCreatedDate();
        $contractSection->setUpdatedDate();
        $manager->persist($contractSection);
        $manager->flush();
    }
}