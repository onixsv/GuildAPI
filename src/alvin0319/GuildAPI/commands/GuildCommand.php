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

namespace alvin0319\GuildAPI\commands;

use alvin0319\GuildAPI\form\hasGuild\GuildMainForm as HasGuildMainForm;
use alvin0319\GuildAPI\form\noGuild\GuildMainForm as NoGuildMainForm;
use alvin0319\GuildAPI\Guild;
use alvin0319\GuildAPI\GuildAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

class GuildCommand extends Command implements PluginOwned, CommandExecutor{

	public function __construct(){
		parent::__construct("길드", "길드 UI를 엽니다.");
		$this->setPermission("guildapi.command.ui");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
		return $this->onCommand($sender, $this, $commandLabel, $args);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(!$this->testPermissionSilent($sender))
			return false;
		if($sender instanceof Player){
			if(($guild = GuildAPI::getInstance()->getGuildByPlayer($sender)) instanceof Guild){
				$sender->sendForm(new HasGuildMainForm($sender, $guild));
			}else{
				$sender->sendForm(new NoGuildMainForm());
			}
		}
		return true;
	}

	/**
	 * @return GuildAPI
	 */
	public function getOwningPlugin() : Plugin{
		return GuildAPI::getInstance();
	}
}