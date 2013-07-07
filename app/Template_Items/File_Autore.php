<?php

namespace app\Template_Items;

use lib\Templating\Template_Item;

class File_Autore extends File {
// nome che mi fa comodo chiamare nel loop => nome della classe del template item
	protected $sub_items_names = array('autore' => 'Utente');
}
