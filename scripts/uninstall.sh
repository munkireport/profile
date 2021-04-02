#!/bin/bash

# Remove profile.py script
rm -f "${MUNKIPATH}preflight.d/profile.py"

# Remove profile.plist file
rm -f "${MUNKIPATH}preflight.d/cache/profile.(plist|sh)"
