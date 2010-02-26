<?php
// "trinity", "trinity2" or "mangos"
define(PROJECT,"trinity2");

/* do not touch down here :) */

define(PROJ_AUTHOR,"EleGoS & Maikash");
define(PROJ_LICENCE,"GNU/GPL v.3.0");
define(PROJ_VERSION,"0.3");

if(PROJECT == "trinity") {
	define(DATA_FIELD_ARENA,1563); // PLAYER_FIELD_ARENA_CURRENCY
	define(DATA_FIELD_HONOR,1562); // PLAYER_FIELD_HONOR_CURRENCY
	define(DATA_FIELD_LEVEL,34); // UNIT_FIELD_LEVEL
	define(DATA_FIELD_MONEY,1431); // PLAYER_FIELD_COINAGE
	define(DATA_FIELD_GENDER,36); // UNIT_FIELD_BYTES_0
} else if(PROJECT == "trinity2") {
	define(DATA_FIELD_ARENA,1247); // PLAYER_FIELD_ARENA_CURRENCY
	define(DATA_FIELD_HONOR,1246); // PLAYER_FIELD_HONOR_CURRENCY
	define(DATA_FIELD_LEVEL,53); // UNIT_FIELD_LEVEL
	define(DATA_FIELD_MONEY,1144); // PLAYER_FIELD_COINAGE
	define(DATA_FIELD_GENDER,22); // UNIT_FIELD_BYTES_0
} else if(PROJECT == "mangos") {
	define(DATA_FIELD_ARENA,1247); // PLAYER_FIELD_ARENA_CURRENCY
	define(DATA_FIELD_HONOR,1246); // PLAYER_FIELD_HONOR_CURRENCY
	define(DATA_FIELD_LEVEL,53); // UNIT_FIELD_LEVEL
	define(DATA_FIELD_MONEY,1144); // PLAYER_FIELD_COINAGE
	define(DATA_FIELD_GENDER,22); // UNIT_FIELD_BYTES_0
}

// genders
define(GENDER_MALE,		0);
define(GENDER_FEMALE,	1);
define(GENDER_NONE,		2);

// items slots
define(SLOT_HEAD,		0);
define(SLOT_NECK,		1);
define(SLOT_SHOULDERS,	2);
define(SLOT_SHIRT,		3);
define(SLOT_CHEST,		4);
define(SLOT_WAIST,		5);
define(SLOT_LEGS,		6);
define(SLOT_FEET,		7);
define(SLOT_WRISTS,		8);
define(SLOT_HANDS,		9);
define(SLOT_FINGER1,	10);
define(SLOT_FINGER2,	11);
define(SLOT_TRINKET1,	12);
define(SLOT_TRINKET2,	13);
define(SLOT_BACK,		14);
define(SLOT_MAIN_HAND,	15);
define(SLOT_OFF_HAND,	16);
define(SLOT_RANGED,		17);
define(SLOT_TABARD,		18);
define(SLOT_EMPTY,		19);

// races
define(RACE_HUMAN,		1);
define(RACE_ORC,		2);
define(RACE_DWARF,		3);
define(RACE_NIGHT_ELF,	4);
define(RACE_UNDEAD,		5);
define(RACE_TAUREN,		6);
define(RACE_GNOME,		7);
define(RACE_TROLL,		8);
define(RACE_BLOOD_ELF,	10);
define(RACE_DRAENEI,	11);

// classes
define(CLASS_WARRIOR,		1);
define(CLASS_PALADIN,		2);
define(CLASS_HUNTER,		3);
define(CLASS_ROGUE,			4);
define(CLASS_PRIEST,		5);
define(CLASS_DEATH_KNIGHT,	6);
define(CLASS_SHAMAN,		7);
define(CLASS_MAGE,			8);
define(CLASS_WARLOCK,		9);
define(CLASS_DRUID,			11);
?>
