#!/bin/bash

if [[ -f "$1" ]]; then
  sed -i 's/\r$//' "$1"
fi