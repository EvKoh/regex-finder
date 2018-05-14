<?php

namespace App\Service;

/**
 * Class AbstractCommand
 * @package App\Service
 */
class RegexCreator
{
    /**
     * @var array
     */
    private static $regex = [];

    /**
     * @var array
     */
    private static $recipes = [];

    /**
     * RegexCreator constructor.
     */
    public function __construct()
    {
        self::createRecipes();
    }

    /**
     * @param string $groupName
     * @return array
     */
    public function getByGroup(string $groupName): array
    {
        return call_user_func_array([$this, 'createRegex' . ucfirst($groupName)], []);
    }

    /**
     * @return array
     */
    private static function createRegexTranslate(): array
    {
        $notAllowedJs = '(?!\()';
        $notAllowedJs .= '(?!\))';
        $jsAnyCharacter = '((' . $notAllowedJs . '.)*)';

        $jsSufixe = '(,' . $jsAnyCharacter . ')*';

        self::$regex['translate'][] = [
            'regexContent' => '/Translator\.trans\(' . self::$recipes['string'] . $jsSufixe . '\)/im',
            'regexFileName' => self::createRegexExtensions(['js', 'twig'])
        ];

        $notAllowedTwig = '(?!\[)';
        $notAllowedTwig .= '(?!\])';
        $twigAnyCharacter = '((' . $notAllowedTwig . '.)*)';

        $twigSufix = '(\|uppercase)*';
        $twigSufix .= '(:\[' . $twigAnyCharacter . '\])*'; # parameters
        $twigSufix .= '(:\'[a-zA-Z]{0,20}\')*'; # domain

        self::$regex['translate'][] = [
            'regexContent' => '/\[\%' . self::$recipes['string'] . '\|' . self::$recipes['space'] . 'trans' . $twigSufix . self::$recipes['space'] . '\%\]/im',
            'regexFileName' => self::createRegexExtensions(['js', 'twig'])
        ];

        var_dump(self::$regex['translate']);

        return self::$regex['translate'];
    }

    /**
     * @return array
     */
    private static function createRegexTranslateConcat(): array
    {
        $notAllowedJs = '(?!\()';
        $notAllowedJs .= '(?!\))';
        $jsAnyCharacter = '((' . $notAllowedJs . '.)*)';
        $jsConcat = self::$recipes['space'] . '\+' . self::$recipes['space'];
        $jsConcatLeft = '(' . $jsAnyCharacter . $jsConcat . ')';
        $jsConcatRight = '(' . $jsConcat . $jsAnyCharacter . ')';
        $jsConcatLeftAndRight = '((' . $jsConcatLeft . self::$recipes['string'] . ')|(' . self::$recipes['string'] . $jsConcatRight . '))';

        $jsSufixe = '(,' . $jsAnyCharacter . ')*';

        self::$regex['translateConcat'][] = [
            'regexContent' => '/Translator\.trans\(' . $jsConcatLeftAndRight . $jsSufixe . '\)/im',
            'regexFileName' => self::createRegexExtensions(['js', 'twig'])
        ];

        $notAllowedTwig = '(?!\[)';
        $notAllowedTwig .= '(?!\])';
        $twigAnyCharacter = '((' . $notAllowedTwig . '.)*)';

        $twigSufix = '(\|uppercase)*';
        $twigSufix .= '(:\[' . $twigAnyCharacter . '\])*'; # parameters
        $twigSufix .= '(:\'[a-zA-Z]{0,20}\')*'; # domain

        $twigConcat = self::$recipes['space'] . '~' . self::$recipes['space'];
        $twigConcatLeft = '(' . $twigAnyCharacter . $twigConcat . ')';
        $twigConcatRight = '(' . $twigConcat . $twigAnyCharacter . ')';
        $twigConcatLeftAndRight = '((' . $twigConcatLeft . self::$recipes['string'] . ')|(' . self::$recipes['string'] . $twigConcatRight . '))';

        self::$regex['translateConcat'][] = [
            'regexContent' => '/\[\%' . $twigConcatLeftAndRight . '\|' . self::$recipes['space'] . 'trans' . $twigSufix . self::$recipes['space'] . '\%\]/im',
            'regexFileName' => self::createRegexExtensions(['js', 'twig'])
        ];

        var_dump(self::$regex['translateConcat']);

        return self::$regex['translateConcat'];
    }


    /**
     *
     */
    private static function createRecipes()
    {
        self::$recipes['space'] = "\s*";
        self::$recipes['string'] = self::$recipes['space'] . "('|\")" . "(.*)" . "('|\")" . self::$recipes['space'];
    }

    /**
     * @param array $exts
     * @return string
     */
    private static function createRegexExtensions(array $exts = [])
    {
        $extsRegex = [];
        foreach ($exts as $ext) {
            $extsRegex[] = '\.' . $ext;
        }
        return '/' . implode('|', $extsRegex) . '/';
    }
}