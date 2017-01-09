<?php
/**
 * @author Massimiliano Pasquesi <massimiliano.pasquesi@bayard-presse.com>
 * @copyright 2016 Bayard Presse (http://www.groupebayard.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Bayard\Bundle\SharedToolsBundle\Twig;

use Bayard\Bundle\SharedToolsBundle\Helper\VersionHelper;

class BayardVersionExtension extends \Twig_Extension
{
    protected $versionHelper;

    public function __construct(VersionHelper $versionHelper)
    {
        $this->versionHelper = $versionHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('bayard_version' ,array($this, 'bayardVersion')),
            new \Twig_SimpleFunction('bayard_git_branch' ,array($this, 'bayardGitBranch')),
        ];
    }

    /**
     * Returns the current version
     *
     * @return string
     */
    public function bayardVersion()
    {
        return $this->versionHelper->getAppVersion();
    }

    /**
     * Returns the current git branch
     *
     * @return string
     */
    public function bayardGitBranch()
    {
        return $this->versionHelper->getCurrentGitBranch();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bayard_version_extension';
    }
}
