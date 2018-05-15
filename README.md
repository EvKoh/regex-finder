# Regex-Finder

**Build** and **Find** with complex regex in any project

# Translate Examples

These examples works with https://github.com/willdurand/BazingaJsTranslationBundle

Simple finder (in tag translate)
```
php bin/console app:finder "translate" "$HOME/projects/example"
```
Translate contains concat (js/twig)
```
php bin/console app:finder "translateConcat" "$HOME/projects/example"
```

Begin with... (in tag translate)
```
php bin/console app:finder "translate" "$HOME/projects/example" "begin" ""
```

End with... (in tag translate)
```
php bin/console app:finder "translate" "$HOME/projects/example" "" "end"
```

Begin and end with... (in tag translate)
```
php bin/console app:finder "translate" "$HOME/projects/example" "begin" "end"
```
