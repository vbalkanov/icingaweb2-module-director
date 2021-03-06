<?php

namespace Icinga\Module\Director\Objects;

use Icinga\Data\Db\DbConnection;
use Icinga\Module\Director\Web\Form\DirectorObjectForm;

class IcingaHost extends IcingaObject
{
    protected $table = 'icinga_host';

    protected $defaultProperties = array(
        'id'                    => null,
        'object_name'           => null,
        'object_type'           => null,
        'disabled'              => 'n',
        'display_name'          => null,
        'address'               => null,
        'address6'              => null,
        'check_command_id'      => null,
        'max_check_attempts'    => null,
        'check_period_id'       => null,
        'check_interval'        => null,
        'retry_interval'        => null,
        'enable_notifications'  => null,
        'enable_active_checks'  => null,
        'enable_passive_checks' => null,
        'enable_event_handler'  => null,
        'enable_flapping'       => null,
        'enable_perfdata'       => null,
        'event_command_id'      => null,
        'flapping_threshold'    => null,
        'volatile'              => null,
        'zone_id'               => null,
        'command_endpoint_id'   => null,
        'notes'                 => null,
        'notes_url'             => null,
        'action_url'            => null,
        'icon_image'            => null,
        'icon_image_alt'        => null,
        'has_agent'             => null,
        'master_should_connect' => null,
        'accept_config'         => null,
    );

    protected $relations = array(
        'check_command'    => 'IcingaCommand',
        'event_command'    => 'IcingaCommand',
        'check_period'     => 'IcingaTimePeriod',
        'command_endpoint' => 'IcingaEndpoint',
        'zone'             => 'IcingaZone',
    );

    protected $booleans = array(
        'enable_notifications'  => 'enable_notifications',
        'enable_active_checks'  => 'enable_active_checks',
        'enable_passive_checks' => 'enable_passive_checks',
        'enable_event_handler'  => 'enable_event_handler',
        'enable_flapping'       => 'enable_flapping',
        'enable_perfdata'       => 'enable_perfdata',
        'volatile'              => 'volatile',
        'has_agent'             => 'has_agent',
        'master_should_connect' => 'master_should_connect',
        'accept_config'         => 'accept_config'
    );

    protected $intervalProperties = array(
        'check_interval' => 'check_interval',
        'retry_interval' => 'retry_interval',
    );

    protected $supportsCustomVars = true;

    protected $supportsGroups = true;

    protected $supportsImports = true;

    protected $supportsFields = true;

    public static function enumProperties(DbConnection $connection = null)
    {
        $properties = array_merge(
            array('name'),
            static::create()->listProperties()
        );
        $props = mt('director', 'Properties');
        $vars  = mt('director', 'Custom variables');
        $properties = array(
            $props => array_combine($properties, $properties),
            $vars => array()
        );

        unset($properties[$props]['object_name']);
        unset($properties[$props]['object_type']);

        if ($connection !== null) {
            foreach ($connection->fetchDistinctHostVars() as $var) {
                if ($var->datatype) {
                    $properties[$vars]['vars.' . $var->varname] = sprintf(
                        '%s (%s)',
                        $var->varname,
                        $var->caption
                    );
                } else {
                    $properties[$vars]['vars.' . $var->varname] = $var->varname;
                }
            }
        }

        //$properties['vars.*'] = 'Other custom variable';
        ksort($properties[$vars]);
        ksort($properties[$props]);
        return $properties;
    }

    public function getCheckCommand()
    {
        $id = $this->getResolvedProperty('check_command_id');
        return IcingaCommand::loadWithAutoIncId(
            $id,
            $this->getConnection()
        );
    }

    public function hasCheckCommand()
    {
        return $this->getResolvedProperty('check_command_id') !== null;
    }

    /**
     * Internal property, will not be rendered
     *
     * Avoid complaints for method names with underscore:
     * @codingStandardsIgnoreStart
     *
     * @return string
     */
    protected function renderHas_Agent()
    {
        return '';
    }

    /**
     * Internal property, will not be rendered
     *
     * @return string
     */
    protected function renderMaster_should_connect()
    {
        return '';
    }

    /**
     * Internal property, will not be rendered
     *
     * @return string
     */
    protected function renderAccept_config()
    {
        // @codingStandardsIgnoreEnd
        return '';
    }
}
