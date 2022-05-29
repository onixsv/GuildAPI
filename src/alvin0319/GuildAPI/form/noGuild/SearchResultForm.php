<?php

/*
 *
 *  /$$$$$$            /$$ /$$       /$$  /$$$$$$  /$$$$$$$  /$$$$$$
 * /$$__  $$          |__/| $$      | $$ /$$__  $$| $$__  $$|_  $$_/
 * | $$  \__/ /$$   /$$ /$$| $$  /$$$$$$$| $$  \ $$| $$  \ $$  | $$
 * | $$ /$$$$| $$  | $$| $$| $$ /$$__  $$| $$$$$$$$| $$$$$$$/  | $$
 * | $$|_  $$| $$  | $$| $$| $$| $$  | $$| $$__  $$| $$____/   | $$
 * | $$  \ $$| $$  | $$| $$| $$| $$  | $$| $$  | $$| $$        | $$
 * |  $$$$$$/|  $$$$$$/| $$| $$|  $$$$$$$| $$  | $$| $$       /$$$$$$
 *  \______/  \______/ |__/|__/ \_______/|__/  |__/|__/      |______/
 *
 * Copyright (C) 2020 alvin0319
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace alvin0319\GuildAPI\form\noGuild;

use alvin0319\GuildAPI\form\all\GuildInfoForm;
use alvin0319\GuildAPI\Guild;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function count;
use function is_int;

class SearchResultForm implements Form{

	/** @var Guild[] */
	protected array $founded = [];

	public function __construct(array $founded){
		$this->founded = $founded;
	}

	public function jsonSerialize() : array{
		$arr = [];
		foreach($this->founded as $guild){
			$arr[] = ["text" => $guild->getName()];
		}
		return [
			"type" => "form",
			"title" => count($this->founded) . "개의 길드를 찾았습니다.",
			"content" => "길드를 클릭하시면 해당 길드의 정보로 넘어갑니다...",
			"buttons" => $arr
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_int($data)){
			if(isset($this->founded[$data])){
				$player->sendForm(new GuildInfoForm($player, $this->founded[$data]));
			}
		}
	}
}
