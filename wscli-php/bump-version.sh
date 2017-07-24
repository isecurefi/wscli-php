#!/bin/bash

set -e

if [ $# -ne 1 ]; then
  echo "Usage: `basename $0` <tag>"
  exit 1
fi

TAG=$1

#
# Tag & build master branch
#
git checkout master
git tag -a -m "**Release v${TAG}**" ${TAG}
make release

#
# Copy executable file into GH pages
#

cp wscli.phar /tmp/
cp wscli.phar.pubkey /tmp/
cd ..
pwd

git checkout gh-pages
pwd
cp /tmp/wscli.phar .
cp /tmp/wscli.phar.pubkey .
SHA1=$(openssl sha1 wscli.phar | cut -f 2 -d ' ')
echo $SHA1 > wscli.phar.version
git add wcsli.phar
git add wscli.phar.pubkey
git add wscli.phar.version
git commit -m "Version $TAG with sha1 $SHA1" wscli.phar.version wscli.phar wscli.phar.pubkey
git push

git checkout master
git push --tags
