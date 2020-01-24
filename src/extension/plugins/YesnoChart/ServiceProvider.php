<?php

namespace Acms\Plugins\YesnoChart;

use ACMS_App;
use DB;
use SQL;
use Field;
use Config;
use Acms\Services\Common\HookFactory;
use Acms\Services\Common\CorrectorFactory;
use Acms\Services\Common\ValidatorFactory;
use Acms\Services\Common\InjectTemplate;

class ServiceProvider extends ACMS_App
{
    /**
     * @var string
     */
    public $version = '1.0.0';

    /**
     * @var string
     */
    public $name = 'YesnoChart';

    /**
     * @var string
     */
    public $author = 'maki_t2nd';

    /**
     * @var bool
     */
    public $module = false;

    /**
     * @var bool|string
     */
    public $menu = false;

    /**
     * @var string
     */
    public $desc = 'Yes/Noチャートのプラグインです。';

    /**
     * サービスの初期処理
     */
    public function init()
    {
        // $hook = HookFactory::singleton();
        // $hook->attach('SampleHook', new Hook);

        $corrector = CorrectorFactory::singleton();
        $corrector->attach('yncCorrector', new Corrector);

        // $validator = ValidatorFactory::singleton();
        // $validator->attach('SampleValidator', new Validator);

        $inject = InjectTemplate::singleton();
        $inject->add('admin-category-field', PLUGIN_DIR . 'YesnoChart/theme/admin/category/field.html');

        $categoryField = loadCategoryField(CID);
        $is_yesno = $categoryField->get('ync_is_yesno');
        if($is_yesno) {
          $inject->add('admin-entry-field', PLUGIN_DIR . 'YesnoChart/theme/admin/entry/field.html');
        }
    }

    /**
     * インストールする前の環境チェック処理
     *
     * @return bool
     */
    public function checkRequirements()
    {
        return true;
    }

    /**
     * インストールするときの処理
     * データベーステーブルの初期化など
     *
     * @return void
     */
    public function install()
    {
      
    }

    /**
     * アンインストールするときの処理
     * データベーステーブルの始末など
     *
     * @return void
     */
    public function uninstall()
    {

    }

    /**
     * アップデートするときの処理
     *
     * @return bool
     */
    public function update()
    {
        return true;
    }

    /**
     * 有効化するときの処理
     *
     * @return bool
     */
    public function activate()
    {
        $DB = DB::singleton(dsn());

        $SQL = SQL::newSelect('module');
        $SQL->addSelect('module_id');
        $SQL->addWhereOpr('module_identifier', 'ync_entry_options');
        $res = $DB->query($SQL->get(dsn()), 'one');

        if(!$res) {
            $mid = $DB->query(SQL::nextval('module_id', dsn()), 'seq');

            $SQL = SQL::newInsert('module');
            $SQL->addInsert('module_id', $mid);
            $SQL->addInsert('module_blog_id', BID);
            $SQL->addInsert('module_name', 'Entry_Summary');
            $SQL->addInsert('module_identifier', 'ync_entry_options');
            $SQL->addInsert('module_label', 'Yes/Noチャート設問用');
            $SQL->addInsert('module_description', '');
            $SQL->addInsert('module_custom_field', 0);
            $SQL->addInsert('module_layout_use', 0);

            $DB->query($SQL->get(dsn()), 'exec');

            $config = new Field();
            $config->set('entry_summary_limit', 999);
            $config->set('entry_summary_hidden_current_entry', 'on');
            Config::saveConfig($config, BID, null, $mid);
        }

        $SQL = SQL::newSelect('module');
        $SQL->addSelect('module_id');
        $SQL->addWhereOpr('module_identifier', 'ync_json');
        $res = $DB->query($SQL->get(dsn()), 'one');

        if(!$res) {
            $mid = $DB->query(SQL::nextval('module_id', dsn()), 'seq');

            $SQL = SQL::newInsert('module');
            $SQL->addInsert('module_id', $mid);
            $SQL->addInsert('module_blog_id', BID);
            $SQL->addInsert('module_name', 'Entry_Summary');
            $SQL->addInsert('module_identifier', 'ync_json');
            $SQL->addInsert('module_label', 'Yes/Noチャート出力JSON用');
            $SQL->addInsert('module_description', '');
            $SQL->addInsert('module_cid_scope', 'global');
            $SQL->addInsert('module_custom_field', 0);
            $SQL->addInsert('module_layout_use', 0);

            $DB->query($SQL->get(dsn()), 'exec');

            $config = new Field();
            $config->set('entry_summary_limit', 999);
            Config::saveConfig($config, BID, null, $mid);
        }

        return true;
    }

    /**
     * 無効化するときの処理
     *
     * @return bool
     */
    public function deactivate()
    {
        return true;
    }
}