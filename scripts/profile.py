#! /usr/bin/python

import subprocess
import plistlib
import os
import sys
import platform
import json
from datetime import datetime, timedelta, tzinfo

def get_profiles_data(cachedir):

    cmd = ['/usr/bin/profiles', '-P', '-o', cachedir+'profile_temp.plist']
    proc = subprocess.Popen(cmd, shell=False, bufsize=-1,
                            stdin=subprocess.PIPE,
                            stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    (output, unused_error) = proc.communicate()

    profile_plist = plistlib.readPlist(cachedir+'profile_temp.plist')

    profile_data = []

    # Process all the profile data
    for profile_user in profile_plist:

        profile = {}

        for inner_user in profile_plist[profile_user]:
            # Process each user's profile data

            for item in inner_user:

                # Set the user level of profile
                if profile_user == "_computerlevel":
                    profile['user'] = "System Level"
                else:
                    profile['user'] = profile_user

                if item == 'ProfileUUID':
                    profile['profile_uuid'] = inner_user[item]
                elif item == 'ProfileDisplayName':
                    profile['profile_name'] = inner_user[item]
                elif item == 'ProfileDescription':
                    profile['profile_description'] = inner_user[item]
                elif item == 'ProfileOrganization':
                    profile['profile_organization'] = inner_user[item]
                elif item == 'ProfileVerificationState':
                    profile['profile_verification_state'] = inner_user[item]
                elif item == 'ProfileUninstallPolicy' or item == 'ProfileRemovalDisallowed':
                    profile['profile_removal_allowed'] = inner_user[item]
                elif item == 'ProfileInstallDate':
                        installed = str(inner_user[item])                    
                        date_str, tz = installed[:-5], installed[-5:]
                        dt_utc = datetime.strptime(date_str.strip(), "%Y-%m-%d %H:%M:%S")
                        dt = dt_utc.replace(tzinfo=FixedOffset(tz))
                        utc_naive = dt.replace(tzinfo=None) - dt.utcoffset()
                        profile['profile_install_date'] = int((utc_naive - datetime(1970, 1, 1)).total_seconds())

                # Process profile payload items
                elif item == 'ProfileItems':
                    for payload in inner_user[item]:
                        for payload_item in payload:

                            if payload_item == 'PayloadType':
                                profile['payload_name'] = payload[payload_item]
                            elif payload_item == 'PayloadDisplayName':
                                profile['payload_display'] = payload[payload_item]
                            elif payload_item == 'PayloadContent':
                                profile['payload_data'] = json.dumps(payload[payload_item])

                        # Add profile to profile_data
                        profile_data.append(profile)

    return profile_data

class FixedOffset(tzinfo):
    """offset_str: Fixed offset in str: e.g. '-0400'"""
    def __init__(self, offset_str):
        sign, hours, minutes = offset_str[0], offset_str[1:3], offset_str[3:]
        offset = (int(hours) * 60 + int(minutes)) * (-1 if sign == "-" else 1)
        self.__offset = timedelta(minutes=offset)
        # NOTE: the last part is to remind about deprecated POSIX GMT+h timezones
        # that have the opposite sign in the name;
        # the corresponding numeric value is not used e.g., no minutes
        '<%+03d%02d>%+d' % (int(hours), int(minutes), int(hours)*-1)
    def utcoffset(self, dt=None):
        return self.__offset
    def tzname(self, dt=None):
        return self.__name
    def dst(self, dt=None):
        return timedelta(0)
    def __repr__(self):
        return 'FixedOffset(%d)' % (self.utcoffset().total_seconds() / 60)

def getMajorOsVersion():
    """Returns the major OS version."""
    os_version_tuple = platform.mac_ver()[0].split('.')
    return int(os_version_tuple[0])

def getMinorOsVersion():
    """Returns the minor OS version."""
    os_version_tuple = platform.mac_ver()[0].split('.')
    return int(os_version_tuple[1])
    
def main():

    """Main"""

    # Check that we're running 10.7 or higher
    if getMajorOsVersion() == 10 and getMinorOsVersion() < 7:
        print "Profiles module requires macOS 10.7 or higher to run"
        exit(0)

    # Set cache directory
    cachedir = '%s/cache' % os.path.dirname(os.path.realpath(__file__))

    # Get results
    info = get_profiles_data(cachedir)

    # Remove temporary plist
    try:
        os.remove(cachedir+'profile_temp.plist')
    except:
        pass

    # Write profile results to cache file
    output_plist = os.path.join(cachedir, 'profile.plist')
    plistlib.writePlist(info, output_plist)
#    print plistlib.writePlistToString(info)

if __name__ == "__main__":
    main()
