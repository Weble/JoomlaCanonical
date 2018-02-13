<?php

defined('_JEXEC') or die;


class PlgSystemCanonical extends JPlugin
{
    /**
     * Application object.
     *
     * @var    JApplicationCms
     * @since  3.5
     */
    protected $app;

    /**
     * Add the canonical uri to the head.
     *
     * @return  void
     *
     * @since   3.5
     */
    public function onAfterDispatch ()
    {
        $doc = $this->app->getDocument();

        if (!$this->app->isClient('site') || $doc->getType() !== 'html') {
            return;
        }

        $sefParams = new \JRegistry(\JPluginHelper::getPlugin('system', 'sef')->params);
        $sefDomain = $sefParams->get('domain', '');

        // Don't add a canonical html tag if no alternative domain has added in SEF plugin domain field.
        if (empty($sefDomain)) {
            return;
        }

        // Check if a canonical html tag already exists (for instance, added by a component).
        $canonical = '';

        foreach ($doc->_links as $linkUrl => $link) {
            if (isset($link['relation']) && $link['relation'] === 'canonical') {
                $canonical = $linkUrl;
                break;
            }
        }

        // If a canonical html tag already exists get the canonical and change it to use the SEF plugin domain field.
        if (!empty($canonical)) {
            // Remove current canonical link.
            unset($doc->_links[$canonical]);

            // Set the current canonical link but use the SEF system plugin domain field.
            $canonical = $sefDomain . \JUri::getInstance($canonical)->toString(array('path'));
        } // If a canonical html doesn't exists already add a canonical html tag using the SEF plugin domain field.
        else {
            $canonical = $sefDomain . \JUri::getInstance()->toString(array('path'));
        }

        // Add the canonical link.
        $doc->addHeadLink(htmlspecialchars($canonical), 'canonical');
    }
}
