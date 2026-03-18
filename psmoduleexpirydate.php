<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\FormBuilderModifier;

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
        return parent::install()
            && $this->installDb()
            && $this->registerHook('actionProductFormDataProviderData')
            && $this->registerHook('actionProductFormBuilderModifier')
            && $this->registerHook('actionAfterCreateProductFormHandler')
            && $this->registerHook('actionAfterUpdateProductFormHandler');
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

    public function hookActionProductFormDataProviderData(array $params): void
    {
        $productId = isset($params['id']) ? (int) $params['id'] : null;

        $params['data']['stock'] = $params['data']['stock'] ?? [];
        $params['data']['stock']['availability'] = $params['data']['stock']['availability'] ?? [];
        $params['data']['stock']['availability']['expiration_date'] = $this->getExpirationDateByProductId($productId);
    }

    public function hookActionProductFormBuilderModifier(array $params): void
    {
        $formBuilder = $params['form_builder'];
        if (!$formBuilder->has('stock')) {
            return;
        }

        $stockFormBuilder = $formBuilder->get('stock');
        if (!$stockFormBuilder->has('availability')) {
            return;
        }

        $availabilityFormBuilder = $stockFormBuilder->get('availability');
        if ($availabilityFormBuilder->has('expiration_date')) {
            return;
        }

        $formBuilderModifier = new FormBuilderModifier();
        $formBuilderModifier->addAfter(
            $availabilityFormBuilder,
            'available_date',
            'expiration_date',
            DatePickerType::class,
            [
                'label' => $this->trans('Date d\'expiration', [], 'Modules.Psmoduleexpirydate.Admin'),
                'required' => false,
                'attr' => [
                    'placeholder' => 'YYYY-MM-DD',
                ],
                'modify_all_shops' => true,
            ]
        );
    }

    public function hookActionAfterCreateProductFormHandler(array $params): void
    {
        $this->saveExpirationDate(
            (int) $params['id'],
            $this->extractExpirationDateFromFormData($params['form_data'] ?? [])
        );
    }

    public function hookActionAfterUpdateProductFormHandler(array $params): void
    {
        $this->saveExpirationDate(
            (int) $params['id'],
            $this->extractExpirationDateFromFormData($params['form_data'] ?? [])
        );
    }

    private function getExpirationDateByProductId(?int $productId): ?string
    {
        if (empty($productId)) {
            return null;
        }

        $value = Db::getInstance()->getValue(
            'SELECT `expiration_date`
             FROM `' . _DB_PREFIX_ . 'psmoduleexpirydate`
             WHERE `id_product` = ' . (int) $productId
        );

        if ($value === false || $value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }

    private function saveExpirationDate(int $productId, ?string $expirationDate): void
    {
        if ($productId <= 0) {
            return;
        }

        $expirationDateValue = null;
        if ($expirationDate !== null && $expirationDate !== '') {
            $expirationDateValue = pSQL($expirationDate);
        }

        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'psmoduleexpirydate` (`id_product`, `expiration_date`)
            VALUES (' . (int) $productId . ', ' . ($expirationDateValue === null ? 'NULL' : '\'' . $expirationDateValue . '\'') . ')
            ON DUPLICATE KEY UPDATE `expiration_date` = ' . ($expirationDateValue === null ? 'NULL' : '\'' . $expirationDateValue . '\'');

        Db::getInstance()->execute($sql);
    }

    private function extractExpirationDateFromFormData(array $formData): ?string
    {
        $expirationDate = $formData['stock']['availability']['expiration_date'] ?? null;
        if ($expirationDate === '' || $expirationDate === null) {
            return null;
        }

        return (string) $expirationDate;
    }
}
