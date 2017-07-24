#!/bin/bash

set -e

GHPAGES="../../../../wscli-php/"

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
    cp wscli.phar ${GHPAGES}wscli.phar
    cd ${GHPAGES}
    git checkout gh-pages
    git add wscli.phar
)

SHA1=$(openssl sha1 wscli.phar | cut -f 2 -d ' ')
if [ -f wscli.phar.pubkey ];
then
    (
        cp wscli.phar.pubkey ${GHPAGES}wscli.phar.pubkey
        cd ${GHPAGES}
        git add wscli.phar.pubkey
    )
fi
(
    cd ${GHPAGES}
    echo $SHA1 > wscli.phar.version
    git add wscli.phar.version
    git commit -m "Version $TAG with sha1 $SHA1" wscli.phar.version wscli.phar
    git push
)
