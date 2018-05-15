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
     * @param array $parameters
     * @return array
     */
    public function getByGroup(string $groupName, array $parameters = []): array
    {
        call_user_func_array([$this, 'createRecipes'], $parameters);
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

        $jsSufixe = '(' . self::$recipes['space'] . ',' . $jsAnyCharacter . ')*';

        self::$regex['translate'][] = [
            'regexContent' => 'Translator\.trans\(' . self::$recipes['string'] . $jsSufixe . '\)',
            'regexFileName' => self::createRegexExtensions(['js'])
        ];

        self::details(
            'Simple in js',
            self::$regex['translate'][0],
            "Translator.trans('begin some string');\n" .
            "Translator.trans('some string end');\n" .
            "Translator.trans('begin some string end');\n" .
            "Translator.trans('some string end', [], var);\n"
        );

        $notAllowedTwig = '(?!\[)';
        $notAllowedTwig .= '(?!\])';
        $twigAnyCharacter = '((' . $notAllowedTwig . '.)*)';

        $twigSufix = '(\|uppercase)*';
        $twigSufix .= '(:\[' . $twigAnyCharacter . '\])*'; # parameters
        $twigSufix .= '(:\'[a-zA-Z]{0,20}\')*'; # domain

        $twigOpen = '(\[\%|\{\{)';
        $twigClose = '(\}\}|\%\])';

        self::$regex['translate'][] = [
            'regexContent' => $twigOpen . self::$recipes['string'] . '\|' . self::$recipes['space'] . 'trans' . $twigSufix . self::$recipes['space'] . $twigClose,
            'regexFileName' => self::createRegexExtensions(['js', 'twig'])
        ];

        self::details(
            'Simple in twig',
            self::$regex['translate'][1],
            "[% 'begin some string'|trans %]\n" .
            "[% 'some string end'|trans %]\n" .
            "[% 'begin some string end'|trans %]\n"
        );

        return self::$regex['translate'];
    }

    /**
     * @param string $title
     * @param array $regex
     * @param string $testString
     */
    private static function details(string $title, array $regex, string $testString = '')
    {
        echo "regex (" . (strlen($regex['regexContent']) + 3) . "): /" . $regex['regexContent'] . "/im\n";
        echo "file mask : " . $regex['regexFileName'] . "\n";
        echo $title . " : https://regex101.com/?regex=" . urlencode($regex['regexContent']) . "&testString=" . urlencode($testString) . "&flags=img&delimiter=" . urlencode('/') . "\n\n";
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

        $jsSufixe = '(' . self::$recipes['space'] . ',' . $jsAnyCharacter . ')*';

        self::$regex['translateConcat'][] = [
            'regexContent' => 'Translator\.trans\(' . $jsConcatLeftAndRight . $jsSufixe . '\)',
            'regexFileName' => self::createRegexExtensions(['js'])
        ];

        self::details(
            'Contact in js',
            self::$regex['translateConcat'][0],
            "Translator.trans('some string' + \"some string\");\n" .
            "Translator.trans('some string' + var);\n" .
            "Translator.trans(var + 'some string');\n"
        );

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

        $twigOpen = '(\[\%|\{\{)';
        $twigClose = '(\}\}|\%\])';

        self::$regex['translateConcat'][] = [
            'regexContent' => $twigOpen . $twigConcatLeftAndRight . '\|' . self::$recipes['space'] . 'trans' . $twigSufix . self::$recipes['space'] . $twigClose,
            'regexFileName' => self::createRegexExtensions(['js', 'twig'])
        ];

        self::details(
            'Concat in twig',
            self::$regex['translateConcat'][1],
            "[% 'some string' ~ \"some string\"|trans %]\n" .
            "[% 'some string' ~ var|trans %]\n" .
            "[% var ~ 'some string'|trans %]\n"
        );

        return self::$regex['translateConcat'];
    }


    /**
     * @param $begin
     * @param $end
     */
    private static function createRecipes($begin = null, $end = null)
    {
        //var_dump($begin);var_dump($end);

        self::$recipes['space'] = "\s*";

        if ((null !== $begin && '' === $begin) && null === $end) {
            $begin = preg_quote($begin);
            $string = '(' . $begin . '(.*))';
        } elseif (null === $begin && (null !== $end && '' === $end)) {
            $end = preg_quote($end);
            $string = '((.*)' . $end . ')';
        } elseif (null !== $begin && null !== $end) {
            $begin = preg_quote($begin);
            $end = preg_quote($end);
            $string = '(' . $begin . '(.*)' . $end . ')';
        } elseif ((null !== $begin && '' !== $begin) && null === $end) { # contains
            $begin = preg_quote($begin);
            $string = '((.*)' . $begin . '(.*))';
        } else {
            $string = '(.*)';
        }

        self::$recipes['string'] = self::$recipes['space'] . "('|\")" . $string . "('|\")" . self::$recipes['space'];
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
