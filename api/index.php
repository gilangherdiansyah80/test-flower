<?php

/**
 * Laravel on Vercel
 * This file acts as the serverless entry point to your Laravel application.
 */

// Forward requests to the original Laravel public/index.php
require __DIR__ . '/../public/index.php';
