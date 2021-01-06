<?php

/**
 * PHP Wrapper to Interact with Vultr 2.0 API
 *
 * @package Vultr
 * @version 2.0
 * @author  https://github.com/dutchie027
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @see     https://github.com/dutche027/vultr-php
 * @see     https://packagist.org/packages/dutchie027/vultr
 * @see     https://www.vultr.com/api/v2
 *
 */

namespace dutchie027\Vultr;

class StartupScripts
{

    /**
     * Reference to \API object
     *
     * @var object
     */
    protected $api;

     /**
     * Array of All Script IDs
     *
     * @var array
     */
    public $ids = [];

    private $validStartupTypes = [
        "pxe",
        "boot"
    ];

    private $d_startup_type = "boot";

    /**
     * Default label to use when creating script or updating Script
     *
     * @var string
     */
    protected $d_label = "";

    /**
     * Array of Startup Script Information
     *
     * @var array
     */
    public $startupScripts = [];

    /**
     * Count of Total Scripts
     *
     * @var int
     */
    protected $total_startup_scripts;

    /**
     * __construct
     * Takes reference from \API
     *
     * @param object $api API
     *
     * @return object
     *
     */
    public function __construct(API $api)
    {
        $this->api = $api;
        $this->loadStartupScripts();
    }

    /**
     * listIds
     * Prints Instance IDs to stdout
     *
     *
     * @return void
     *
     */
    public function listIds()
    {
        foreach ($this->ids as $id) {
            print $id . PHP_EOL;
        }
    }

     /**
     * listStartupScripts
     * Lists Startup Scripts
     *
     *
     * @return string
     *
     */
    public function listStartupScripts()
    {
        return $this->api->makeAPICall('GET', $this->api::STARTUP_SCRIPTS_URL);
    }

    /**
     * deleteStartupScript
     * Deletes Startup Script
     *
     * @var string $id
     *
     * @return string
     *
     */
    public function deleteStartupScript($id)
    {
        return $this->api->makeAPICall('DELETE', $this->api::STARTUP_SCRIPTS_URL . "/" . $id);
    }

    /**
     * getStartupScript
     * Get Startup Script Information
     *
     * @var string $id
     *
     * @return string
     *
     */
    public function getStartupScript($id)
    {
        return $this->api->makeAPICall('GET', $this->api::STARTUP_SCRIPTS_URL . "/" . $id);
    }

     /**
     * loadStartupScripts
     * Loads Startup Script Information in to arrays
     *
     *
     * @return void
     *
     */
    public function loadStartupScripts()
    {
        $sa = json_decode($this->listStartupScripts(), true);
        foreach ($sa['startup_scripts'] as $startup) {
            $id = $startup['id'];
            $this->ids[] = $id;
            $this->startupScritps[$id]['date_created'] = $startup['date_created'];
            $this->startupScritps[$id]['date_modified'] = $startup['date_modified'];
            $this->startupScritps[$id]['name'] = $startup['name'];
            $this->startupScritps[$id]['type'] = $startup['type'];
        }
        $this->total_startup_scripts = $sa['meta']['total'];
    }

    /**
     * updateStartupScript
     * Updates description of Snapshot
     *
     * @param array $options
     *
     * @return string
     *
     */
    public function updateStartupScript($oa)
    {
        if (in_array($oa['id'], $this->ids)) {
            $url = $this->api::STARTUP_SCRIPTS_URL . "/" . $oa['id'];
        } else {
            print "That Startup Script ID isn't associated with your account";
            exit;
        }
        (isset($oa['name'])) ? $ba['name'] = $oa['name'] : null;
        (isset($oa['script'])) ? $ba['script'] = $oa['script'] : null;
        (isset($oa['type'])) ? $ba['type'] = $oa['type'] : null;
        $body = json_encode($ba);
        return $this->api->makeAPICall('PATCH', $url, $body);
    }

    /**
     * createStartupScript
     * Creates a Startup Script
     *
     * @param array $options
     *
     * @return string
     *
     */
    public function createStartupScript($oa)
    {
        if (!isset($oa['type'])) {
            $ba['type'] = $this->d_startup_type;
        } else {
            if (in_array($oa['type'], $this->validStartupTypes)) {
                $ba['type'] = $oa['type'];
            } else {
                print "Startup Script Type is invalid";
                exit;
            }
        }
        if (!isset($oa['name'])) {
            print "Startup Script Name Required";
            exit;
        } else {
            $ba['name'] = $oa['name'];
        }
        if (!isset($oa['script'])) {
            print "Startup Script Missing";
            exit;
        } else {
            $ba['script'] = $oa['script'];
        }
        $body = json_encode($ba);
        return $this->api->makeAPICall('POST', $this->api::SNAPSHOTS_URL, $body);
    }
}
