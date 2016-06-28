<?php

namespace App\Util;

/**
 * Helper class
 *
 * This is a general purpose class which resolves a particular template alias against the template index defined in
 * settings and returns the template path
 *
 * @package SlimSession
 */
class TemplateResolver
{
    public static function getTemplate ($settings, $alias)
    {
        if (!$settings) {
            throw new \Exception ('Invalid [settings] recieved');
        }

        if (!isset($settings['templates'])) {
            throw new \Exception ('Template index array not found in settings');
        }

        $templateIndex = $settings['templates'];
        if (!isset($templateIndex[$alias])) {
            throw new \Exception ("Template index [$alias] not found in settings template index array");
        }

        if (!$templateIndex[$alias]) {
            throw new \Exception ("Invalid alias [$alias] found in settings template index array");
        }

        return $templateIndex[$alias];
    }
}

