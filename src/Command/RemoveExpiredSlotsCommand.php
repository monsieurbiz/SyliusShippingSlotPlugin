<?php

/*
 * This file is part of Monsieur Biz' Shipping Slot plugin for Sylius.
 *
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusShippingSlotPlugin\Command;

use MonsieurBiz\SyliusShippingSlotPlugin\Remover\SlotRemoverInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RemoveExpiredSlotsCommand extends Command
{
    protected static $defaultName = 'monsieurbiz:shipping-slot:remove-expired-slots';

    private ParameterBagInterface $parameterBag;
    private SlotRemoverInterface $slotRemover;

    public function __construct(ParameterBagInterface $parameterBag, SlotRemoverInterface $slotRemover)
    {
        $this->parameterBag = $parameterBag;
        $this->slotRemover = $slotRemover;
        parent::__construct(static::$defaultName);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Removes slots that have been idle for a period set in `monsieurbiz_sylius_shipping_slot.expiration.slot` configuration key.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        unset($input); // For PHP MD
        /** @var string $expirationPeriod */
        $expirationPeriod = $this->parameterBag->get('monsieurbiz_sylius_shipping_slot.slot_expiration_period');
        $output->writeln(sprintf(
            'Command will remove slots from cart that have been idle for <info>%s</info>.',
            $expirationPeriod
        ));

        $this->slotRemover->removeIdleSlots($expirationPeriod);

        return 0;
    }
}
