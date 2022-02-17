<?php

namespace OCA\RocketIntegration\Sections;

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class RocketChatAdminSection implements IIconSection {
    public function __construct(IL10N $l, IURLGenerator $urlGenerator) {
        $this->l = $l;
        $this->urlGenerator = $urlGenerator;
    }

    public function getIcon(): string {
        return $this->urlGenerator->imagePath('rocket_integration', 'rocket-logo-black.png');
    }

    public function getID(): string {
        return 'rocketchat';
    }

    public function getName(): string {
        return $this->l->t('Rocket Chat');
    }

    public function getPriority(): int {
        return 98;
    }
}
