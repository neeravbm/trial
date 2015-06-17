#!/bin/bash
start=`date +%s`
for run in {1..100}
do
	tests/bin/paratest --phpunit=tests/bin/phpunit --processes=4 --no-test-tokens --log-junit="tests/output.xml" --configuration=tests/phpunit.xml
done
end=`date +%s`

runtime=$((end-start))

echo $runtime
