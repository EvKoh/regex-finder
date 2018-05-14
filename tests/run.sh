#!/usr/bin/env bash

searchPath="/home/dev/projects/regex-finder/tests/sources";

php bin/console app:finder "translate" "${searchPath}"

echo -e "\n";

php bin/console app:finder "translateConcat" "${searchPath}"