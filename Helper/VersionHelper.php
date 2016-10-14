<?php
/**
 * @author Massimiliano Pasquesi <massimiliano.pasquesi@bayard-presse.com>
 * @copyright 2016 Bayard Presse (http://www.groupebayard.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Bayard\Bundle\SharedToolsBundle\Helper;

class VersionHelper
{
    const VERSION_FILE = 'VERSION';
    protected $kernelRootDir;

    public function __construct($kernel_root_dir)
    {
        $this->kernelRootDir = $kernel_root_dir;
    }

    protected function getRootDir($with_final_slash = false)
    {
        $rootdir = realpath($this->kernelRootDir . '/..');

        return ($with_final_slash === true) ?
            $rootdir . '/' :
            $rootdir ;
    }

    protected function getVersionFilePath()
    {
        $filename = $this->getRootDir(true) . self::VERSION_FILE;

        return $filename;
    }

    public function getAppVersion()
    {
        $version = file_get_contents($this->getVersionFilePath());

        return $version;
    }

    /**
     * Returns the current git branch
     *
     * @return string
     */
    public function getCurrentGitBranch()
    {
        $git_branch = shell_exec("git branch | /bin/grep '*'");
        $tagcheckstr = '(';
        $versioncheckstr = 'v-';
        if (strpos($git_branch, $tagcheckstr) !== false) {
            $git_branch = substr($git_branch, strpos($git_branch, $versioncheckstr));
            $git_branch = "tag: "  . substr($git_branch, 0, -2);
        } else {
            $git_branch = "branch: " . substr($git_branch, 2);
        }


        return $git_branch;
    }

    public function setAppVersion($version)
    {
        $result = file_put_contents($this->getVersionFilePath(), $version);

        return $result;
    }
}