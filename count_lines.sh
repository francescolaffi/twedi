#!/bin/sh
./cloc.pl --unicode webroot > lines_of_code.txt
echo "\n\n" >> lines_of_code.txt
./cloc.pl --unicode webroot/lib >> lines_of_code.txt
echo "\n\n" >> lines_of_code.txt
./cloc.pl --unicode webroot/app >> lines_of_code.txt