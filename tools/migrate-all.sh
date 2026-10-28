#!/bin/bash
# Full content pipeline on a target site (run from the WordPress root; needs WP-CLI).
# 1) Elementor -> blocks (deduplicated)  2) form pages + contact form  3) legacy Forminator forms  4) careers cleanup
set -e
wp() { php -d disable_functions= /usr/local/bin/wp "$@" 2>&1 | grep -v -e 'uninitialized class' -e 'Attempt to read property'; }
W=~/esnad-work
ESNAD_WRITE=1 wp eval-file $W/migrate-elementor.php
wp eval-file $W/create-form-pages.php
ESNAD_WRITE=1 wp eval-file $W/replace-forminator.php
wp eval-file $W/careers.php
wp litespeed-purge all | head -1
