#!/usr/bin/env perl

use strict;
use warnings;

use Text::Xslate;
use IO::All;

my $tx = Text::Xslate->new;
io->file('Dockerfile')->print(
   $tx->render(
      'Dockerfile.tx',
      { ttrss_version => $ENV{'TTRSS-VERSION'} || '1.12' }
   )
);
