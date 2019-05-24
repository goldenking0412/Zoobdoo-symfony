<?php

namespace Erp\StripeBundle\Event;

use Stripe\Event;

class EventGuesser {

    /**
     * @var bool
     */
    private $debug;

    public function __construct($debug) {
        $this->debug = $debug;
    }

    public function guess(Event $stripeEvent) {
        $pieces = $this->guessEventPieces($stripeEvent->type);

        switch ($pieces['kind']) {
            case 'charge':
                $disptachingEvent = new ChargeEvent($stripeEvent);

                return [
                    'type' => constant(ChargeEvent::class . '::' . $pieces['type']),
                    'object' => $disptachingEvent,
                ];
                break;
            case 'invoice':
                $disptachingEvent = new InvoiceEvent($stripeEvent);

                return [
                    'type' => constant(InvoiceEvent::class . '::' . $pieces['type']),
                    'object' => $disptachingEvent,
                ];
                break;
            case 'refund':
                $disptachingEvent = new RefundEvent($stripeEvent);

                return [
                    'type' => constant(RefundEvent::class . '::' . $pieces['type']),
                    'object' => $disptachingEvent,
                ];
                break;
            default:
                if ($this->debug) {
                    throw new \RuntimeException('Event type not recognized.');
                }

                return;
        }
    }

    public function guess1($stripeEvent) {
        $pieces = $this->guessEventPieces($stripeEvent['type']);

        switch ($pieces['kind']) {
            case 'charge':
                $disptachingEvent = new ChargeEvent($stripeEvent);

                return [
                    'type' => constant(ChargeEvent::class . '::' . $pieces['type']),
                    'object' => $disptachingEvent,
                ];
                break;
            case 'invoice':
                $disptachingEvent = new InvoiceEvent($stripeEvent);

                return [
                    'type' => constant(InvoiceEvent::class . '::' . $pieces['type']),
                    'object' => $disptachingEvent,
                ];
                break;
            case 'refund':
                $disptachingEvent = new RefundEvent($stripeEvent);

                return [
                    'type' => constant(RefundEvent::class . '::' . $pieces['type']),
                    'object' => $disptachingEvent,
                ];
                break;
            default:
                if ($this->debug) {
                    throw new \RuntimeException('Event type not recognized.');
                }

                return ['type' => null, 'object' => null];
        }
    }

    public function guessEventPieces($type) {
        /*
         * Guess the event kind.
         *
         * In an event like charge.dispute.closed, the kind is "charge".
         */
        $dotPosition = strpos($type, '.');
        $eventKind = substr($type, 0, $dotPosition);

        /*
         * Guess the constant of the type.
         *
         * In an event like charge.dispute.closed, the type is "DISPUTE_CLOSED".
         */
        $string = str_replace($eventKind . '.', '', $type);
        $string = str_replace('.', '_', $string);
        $eventType = strtoupper($string);

        return [
            'kind' => $eventKind,
            'type' => $eventType,
        ];
    }

}
