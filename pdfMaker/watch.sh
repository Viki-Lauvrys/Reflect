#!/bin/bash

while true; do
  inotifywait -e modify -e create -e delete -e move -r ./ &&
  find ./ -type f \( -name "*.php" -o -name "*.css" \) -print0 |
  xargs -0 -n 1 ./unixify.sh
done