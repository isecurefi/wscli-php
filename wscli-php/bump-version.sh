#!/bin/bash

set -e

if [ $# -ne 1 ]; then
  echo "Usage: `basename $0` <tag>"
  exit 65
fi

TAG=$1

#
# Tag & build master branch
#
git checkout master
git tag -a -m "v${TAG}" ${TAG}
make release

#
# Copy executable file into GH pages
#

(
    git checkout gh-pages
    git add wscli.phar
)

SHA1=$(openssl sha1 wscli.phar | cut -f 2 -d ' ')
if [ -f wscli.phar.pubkey ];
then
    (
        git checkout gh-pages
        git add wscli.phar.pubkey
    )
fi
(
    git checkout gh-pages
    echo $SHA1 > wscli.phar.version
    git add wscli.phar.version
    git commit -m "Version $TAG with sha1 $SHA1" wscli.phar.version wscli.phar
    git push
)

git checkout master
