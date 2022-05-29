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

namespace alvin0319\GuildAPI;

use alvin0319\GuildAPI\commands\GuildCommand;
use alvin0319\GuildAPI\commands\GuildManageCommand;
use alvin0319\GuildAPI\event\GuildCreateEvent;
use alvin0319\GuildAPI\event\GuildRemoveEvent;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use function array_values;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function json_decode;
use function json_encode;
use function strtolower;
use const JSON_BIGINT_AS_STRING;
use const JSON_PRETTY_PRINT;

class GuildAPI extends PluginBase{
	use SingletonTrait;

	public const JOIN_SUCCESS = 0;
	public const CANT_JOIN_ALREADY_MEMBER = 1;
	public const CANT_JOIN_DUE_TO_FULL = 2;

	public const QUIT_SUCCESS = 1;
	public const CANT_QUIT_NOT_MEMBER = 2;

	/** @var Guild[] */
	protected array $guilds = [];

	/** @var EventListener */
	protected EventListener $eventListener;

	protected function onLoad() : void{
		self::setInstance($this);
	}

	protected function onEnable() : void{
		$this->saveResource("settings.yml");
		$this->loadGuilds();
		$this->getServer()->getPluginManager()->registerEvents($listener = new EventListener(), $this);
		$this->eventListener = $listener;

		$this->getServer()->getCommandMap()->registerAll("guild", [
			new GuildCommand(),
			new GuildManageCommand()
		]);
	}

	public function getConfig() : Config{
		return new Config($this->getDataFolder() . "settings.yml", Config::YAML);
	}

	protected function onDisable() : void{
		$this->save();
	}

	public function save() : void{
		$arr = [];
		foreach($this->getGuilds() as $guild){
			$arr[$guild->getName()] = $guild->jsonSerialize();
		}
		file_put_contents($this->getDataFolder() . "guilds.json", json_encode($arr, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING));
	}

	public function getEventListener() : EventListener{
		return $this->eventListener;
	}

	private function loadGuilds() : void{
		$data = file_exists($file = $this->getDataFolder() . "guilds.json") ? json_decode(file_get_contents($file), true) : [];
		foreach($data as $name => $guildData){
			$guild = Guild::jsonDeserialize($guildData);
			$this->guilds[TextFormat::clean($guild->getName())] = $guild;
		}
	}

	public static function convert($player) : string{
		return $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
	}

	public static function message(CommandSender $sender, string $message) : void{
		$sender->sendMessage("§d<§f시스템§d> §f" . $message);
	}

	public function getGuildByPlayer(Player $player) : ?Guild{
		foreach(array_values($this->guilds) as $guild){
			if($guild->isMember($player)){
				return $guild;
			}
		}
		return null;
	}

	public function getGuild(string $name) : ?Guild{
		return $this->guilds[$name] ?? null;
	}

	public function getGuildByPlayerName($value) : ?Guild{
		$value = self::convert($value);
		foreach(array_values($this->guilds) as $guild){
			if($guild->isMember($value)){
				return $guild;
			}
		}
		return null;
	}

	public function changeGuildName(Guild $guild, string $to) : void{
		$originalName = $guild->getName();
		$guild->setName($to);
		unset($this->guilds[$originalName]);
		$this->guilds[$guild->getName()] = $guild;
	}

	/**
	 * @return Guild[]
	 */
	public function getGuilds() : array{
		return array_values($this->guilds);
	}

	/**
	 * @param $player
	 *
	 * @return bool
	 */
	public function hasGuild($player) : bool{
		return $this->getGuildByPlayerName($player) instanceof Guild;
	}

	/**
	 * @param Player $player
	 * @param string $name
	 */
	public function addGuild(Player $player, string $name) : void{
		$guild = new Guild($name, [self::convert($player) => Guild::LEADER], null, Guild::GUILD_TYPE_OPEN);
		(new GuildCreateEvent($guild))->call();
		$this->guilds[$guild->getName()] = $guild;
	}

	public function removeGuild(Guild $guild) : void{
		unset($this->guilds[$guild->getName()]);
		(new GuildRemoveEvent($guild))->call();
		$guild->broadcastMessage("길드가 폐쇄되었습니다.");
	}

	/**
	 * @param        $player
	 * @param Guild  $guild
	 * @param string $role
	 * @param bool   $invitation
	 *
	 * @return int
	 */
	public function addMember($player, Guild $guild, string $role = Guild::MEMBER, bool $invitation = false) : int{
		$player = self::convert($player);
		if($guild->isMember($player)){
			return self::CANT_JOIN_ALREADY_MEMBER;
		}
		if(!$guild->canJoin($player)){
			return self::CANT_JOIN_DUE_TO_FULL;
		}
		$guild->addMember($player, $role, $invitation);
		foreach($this->getGuilds() as $g){
			if($g->getGuildStorage()->hasInvitation($player)){
				$g->getGuildStorage()->removeInvitation($player);
			}
		}
		return self::JOIN_SUCCESS;
	}

	public function removeMember($player, Guild $guild, bool $force) : int{
		$player = self::convert($player);
		if(!$guild->isMember($player)){
			return self::CANT_QUIT_NOT_MEMBER;
		}
		$guild->removeMember($player, $force);
		return self::QUIT_SUCCESS;
	}
}
