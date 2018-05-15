#!/usr/bin/env bash

searchPath="/home/dev/projects/regex-finder/tests/sources";

php bin/console app:finder "translate" "${searchPath}"

echo -e "\n";

php bin/console app:finder "translateConcat" "${searchPath}"

echo -e "\n";

php bin/console app:finder "translate" "${searchPath}" "begin" ""

echo -e "\n";

php bin/console app:finder "translate" "${searchPath}" "" "end"

echo -e "\n";

php bin/console app:finder "translate" "${searchPath}" "begin" "end"

echo -e "\n";

php bin/console app:finder "translate" "${searchPath}" "egi" # simple contains
