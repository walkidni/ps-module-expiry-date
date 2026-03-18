<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class psmoduleexpirydate extends Module
{
    public function __construct()
    {
        $this->name = 'psmoduleexpirydate';
        $this->tab = 'administration';
        $this->version = '0.1.0';
        $this->author = 'Technical Test';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = [
            'min' => '9.0.0',
            'max' => _PS_VERSION_,
        ];

        parent::__construct();

        $this->displayName = $this->trans(
            'Product expiration date',
            [],
            'Modules.Psmoduleexpirydate.Admin'
        );
        $this->description = $this->trans(
            'Adds product expiration-date support.',
            [],
            'Modules.Psmoduleexpirydate.Admin'
        );
    }

    public function install(): bool
    {
        return parent::install() && $this->installDb();
    }

    public function uninstall(): bool
    {
        return $this->uninstallDb() && parent::uninstall();
    }

    private function installDb(): bool
    {
        return (bool) require __DIR__ . '/sql/install.php';
    }

    private function uninstallDb(): bool
    {
        return (bool) require __DIR__ . '/sql/uninstall.php';
    }
}
