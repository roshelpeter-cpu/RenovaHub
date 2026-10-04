<?php

namespace App\Models;

/**
 * A portfolio project is a portfolio item.
 * The shorter name is the one used by the homeowner gallery.
 */
class PortfolioProject extends PortfolioItem
{
    protected $table = 'portfolio_items';
}
