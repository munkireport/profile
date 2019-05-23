#!/bin/bash

# Remove profile script
rm -f "${MUNKIPATH}postflight.d/profile.py"

# Remove profile.txt file
rm -f "${MUNKIPATH}postflight.d/cache/profile.txt"
