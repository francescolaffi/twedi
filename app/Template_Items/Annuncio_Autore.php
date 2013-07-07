<?php

namespace app\Template_Items;

use lib\Templating\Template_Item;

class Annuncio_Autore extends Annuncio {
// nome che mi fa comodo chiamare nel loop => nome della classe del template item
	protected $sub_items_names = array('autore' => 'Utente');
}
