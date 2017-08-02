#!/bin/bash

set -e
ulimit -Sn 4096

if [ $# -ne 1 ]; then
  echo "Usage: `basename $0` <tag>"
  exit 1
fi

TAG=$1

#
# Tag & build master branch
#
git checkout master
git tag -a -m "Release v${TAG}" ${TAG}
make release

#
# Copy executable file into GH pages
#

cp wscli.phar /tmp/
cp wscli.phar.pubkey /tmp/
cd ..
git push --tags

git checkout gh-pages
git pull
cp /tmp/wscli.phar .
cp /tmp/wscli.phar.pubkey .
SHA1=$(openssl sha1 wscli.phar | cut -f 2 -d ' ')
echo $SHA1 > wscli.phar.version
git add wscli.phar
git add wscli.phar.pubkey
git add wscli.phar.version
git commit -m "Version $TAG with sha1 $SHA1" wscli.phar.version wscli.phar wscli.phar.pubkey
git push

git checkout master
