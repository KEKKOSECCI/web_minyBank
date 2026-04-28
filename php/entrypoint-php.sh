#!/bin/bash
set -e

# Avvia apache in primo piano sostituendo il processo della shell
exec apache2-foreground
