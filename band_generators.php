<?php


/**
 * Generate random bandname
 */
function generate_bandname(){
    $list1 = array(
        "", "Weed","Speed","Smoke", "Shred", "Spit", "Carnal", "Riff", "Rock", 
        "Eletric", "Black", "Saint", "The", "Acid", "Kink","Bong", "The Church of", 
        "Flame", "Sacred", "Lord", "Burning", "Red", "Sapphire", "Night", "Cannabis", 
        "Doom", "Lizard", "Dope", "Worship", "Sludge", "Heavy", "Lady", "Toxic", "Bad", 
        "Monster", "Suck", "Leather", "Warrior", "Snow", "Orange", "Banshee", "Devil", 
        "The Dark", "Smoking", "Funeral", "Vapor", "Toke", "Goat", "Unholy", "Eternal", 
        "Spirit", "Stoner", "Pot", "Blood", "Intersteller", "Sacrificial", "Fuzz", "Tone");

    $list2 = array(
        " King"," Queen"," Lizard", "lord"," Jesus", " Fire", " Wizard", " Fuck", 
        "s", " Ripper", " Sluts", "", " Flame", " Witch", " Sabbath", " Stalker", 
        " Acid", " Burn Out", "opolis", " Warden", " Fettish", " Kink", " Pills", 
        " Sky", " Ash", " Sadist", " Masochist", " Preist", " Sacrafice", " Slayer", 
        " Crown", " Bitch", " Thunder", " Masculinity", " Patriarch", " Strike", 
        " Powder", " City", " Sayer", " Seer", " Mask", " Warrior", " Theif", 
        " Cult", " Occult", " Goblin", " Spit", "killer", " Pyre", " Thunder", 
        " Gas", " Fog", " Blaze", " Sacrafice", " Master", " Sucker", " Whip", 
        "zilla", " Sweat", " Eater", " Magnet", " Sword", " Axe", " Caravan", " Fang", 
        " Void", " Misery", " Stoner", " Junkie", " Marijuana", " Breather", "ess", " Tone", 
        " Ritual", " Weed", " Preistess");

    $name = $list1[array_rand($list1, 1)]."".$list2[array_rand($list2, 1)];
    return $name;
}

function generate_bandlogo(){
    $LOGOS_DIR = __DIR__ . DIRECTORY_SEPARATOR . "logos";
    $files = scandir($LOGOS_DIR);

    if ($files === false) {
        throw new RuntimeException("The logos directory could not be read.");
    }

    $logosArr = preg_grep('~\.(jpeg|jpg|png)$~i', $files);

    if ($logosArr === false || $logosArr === array()) {
        throw new RuntimeException("No logo image found in the logos directory.");
    }

    return $logosArr[array_rand($logosArr, 1)];
}

?>