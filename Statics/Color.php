<?php
/**
 * Color class
 *
 * This class contains various constants and static functions relating to colors.
 * 
 * They can be assigned to a Control's properties such as Color, BackColor, etc...
 * 
 * <pre>
 * $label->BackColor = Color::Green;
 * </pre>
 * 
 * @package Statics
 */
final class Color
{
	/**
	 * <DIV class="ColorPreview" style="background:#F0F8FF;"></DIV> 
	 */
	const AliceBlue = '#F0F8FF';
	/**
	 * <DIV class="ColorPreview" style="background:#FAEBD7;"></DIV> 
	 */
	const AntiqueWhite = '#FAEBD7';
	/**
	 * <DIV class="ColorPreview" style="background:#00FFFF;"></DIV> 
	 */
	const Aqua = '#00FFFF';
	/**
	 * <DIV class="ColorPreview" style="background:#7FFFD4;"></DIV> 
	 */
	const Aquamarine = '#7FFFD4';
	/**
	 * <DIV class="ColorPreview" style="background:#F0FFFF;"></DIV> 
	 */
	const Azure = '#F0FFFF';
	/**
	 * <DIV class="ColorPreview" style="background:#F5F5DC;"></DIV> 
	 */
	const Beige = '#F5F5DC';
	/**
	 * <DIV class="ColorPreview" style="background:#FFE4C4;"></DIV> 
	 */
	const Bisque = '#FFE4C4';
	/**
	 * <DIV class="ColorPreview" style="background:#000000;"></DIV> 
	 */
	const Black = '#000000';
	/**
	 * <DIV class="ColorPreview" style="background:#FFEBCD;"></DIV> 
	 */
	const BlanchedAlmond = '#FFEBCD';
	/**
	 * <DIV class="ColorPreview" style="background:#0000FF;"></DIV> 
	 */
	const Blue = '#0000FF';
	/**
	 * <DIV class="ColorPreview" style="background:#8A2BE2;"></DIV> 
	 */
	const BlueViolet = '#8A2BE2';
	/**
	 * <DIV class="ColorPreview" style="background:#A52A2A;"></DIV> 
	 */
	const Brown = '#A52A2A';
	/**
	 * <DIV class="ColorPreview" style="background:#DEB887;"></DIV> 
	 */
	const BurlyWood = '#DEB887';
	/**
	 * <DIV class="ColorPreview" style="background:#5F9EA0;"></DIV> 
	 */
	const CadetBlue = '#5F9EA0';
	/**
	 * <DIV class="ColorPreview" style="background:#7FFF00;"></DIV> 
	 */
	const Chartreuse = '#7FFF00';
	/**
	 * <DIV class="ColorPreview" style="background:#D2691E;"></DIV> 
	 */
	const Chocolate = '#D2691E';
	/**
	 * <DIV class="ColorPreview" style="background:#FF7F50;"></DIV> 
	 */
	const Coral = '#FF7F50';
	/**
	 * <DIV class="ColorPreview" style="background:#6495ED;"></DIV> 
	 */
	const CornflowerBlue = '#6495ED';
	/**
	 * <DIV class="ColorPreview" style="background:#FFF8DC;"></DIV> 
	 */
	const Cornsilk = '#FFF8DC';
	/**
	 * <DIV class="ColorPreview" style="background:#DC143C;"></DIV> 
	 */
	const Crimson = '#DC143C';
	/**
	 * <DIV class="ColorPreview" style="background:#00FFFF;"></DIV> 
	 */
	const Cyan = '#00FFFF';
	/**
	 * <DIV class="ColorPreview" style="background:#00008B;"></DIV> 
	 */
	const DarkBlue = '#00008B';
	/**
	 * <DIV class="ColorPreview" style="background:#008B8B;"></DIV> 
	 */
	const DarkCyan = '#008B8B';
	/**
	 * <DIV class="ColorPreview" style="background:#B8860B;"></DIV> 
	 */
	const DarkGoldenRod = '#B8860B';
	/**
	 * <DIV class="ColorPreview" style="background:#A9A9A9;"></DIV> 
	 */
	const DarkGray = '#A9A9A9';
	/**
	 * <DIV class="ColorPreview" style="background:#006400;"></DIV> 
	 */
	const DarkGreen = '#006400';
	/**
	 * <DIV class="ColorPreview" style="background:#BDB76B;"></DIV> 
	 */
	const DarkKhaki = '#BDB76B';
	/**
	 * <DIV class="ColorPreview" style="background:#8B008B;"></DIV> 
	 */
	const DarkMagenta = '#8B008B';
	/**
	 * <DIV class="ColorPreview" style="background:#556B2F;"></DIV> 
	 */
	const DarkOliveGreen = '#556B2F';
	/**
	 * <DIV class="ColorPreview" style="background:#9932CC;"></DIV> 
	 */
	const DarkOrchid = '#9932CC';
	/**
	 * <DIV class="ColorPreview" style="background:#8B0000;"></DIV> 
	 */
	const DarkRed = '#8B0000';
	/**
	 * <DIV class="ColorPreview" style="background:#E9967A;"></DIV> 
	 */
	const DarkSalmon = '#E9967A';
	/**
	 * <DIV class="ColorPreview" style="background:#8FBC8F;"></DIV> 
	 */
	const DarkSeaGreen = '#8FBC8F';
	/**
	 * <DIV class="ColorPreview" style="background:#483D8B;"></DIV> 
	 */
	const DarkSlateBlue = '#483D8B';
	/**
	 * <DIV class="ColorPreview" style="background:#2F4F4F;"></DIV> 
	 */
	const DarkSlateGray = '#2F4F4F';
	/**
	 * <DIV class="ColorPreview" style="background:#00CED1;"></DIV> 
	 */
	const DarkTurquoise = '#00CED1';
	/**
	 * <DIV class="ColorPreview" style="background:#9400D3;"></DIV> 
	 */
	const DarkViolet = '#9400D3';
	/**
	 * <DIV class="ColorPreview" style="background:#FF8C00;"></DIV> 
	 */
	const Darkorange = '#FF8C00';
	/**
	 * <DIV class="ColorPreview" style="background:#FF1493;"></DIV> 
	 */
	const DeepPink = '#FF1493';
	/**
	 * <DIV class="ColorPreview" style="background:#00BFFF;"></DIV> 
	 */
	const DeepSkyBlue = '#00BFFF';
	/**
	 * <DIV class="ColorPreview" style="background:#696969;"></DIV> 
	 */
	const DimGray = '#696969';
	/**
	 * <DIV class="ColorPreview" style="background:#1E90FF;"></DIV> 
	 */
	const DodgerBlue = '#1E90FF';
	/**
	 * <DIV class="ColorPreview" style="background:#B22222;"></DIV> 
	 */
	const FireBrick = '#B22222';
	/**
	 * <DIV class="ColorPreview" style="background:#FFFAF0;"></DIV> 
	 */
	const FloralWhite = '#FFFAF0';
	/**
	 * <DIV class="ColorPreview" style="background:#228B22;"></DIV> 
	 */
	const ForestGreen = '#228B22';
	/**
	 * <DIV class="ColorPreview" style="background:#FF00FF;"></DIV> 
	 */
	const Fuchsia = '#FF00FF';
	/**
	 * <DIV class="ColorPreview" style="background:#DCDCDC;"></DIV> 
	 */
	const Gainsboro = '#DCDCDC';
	/**
	 * <DIV class="ColorPreview" style="background:#F8F8FF;"></DIV> 
	 */
	const GhostWhite = '#F8F8FF';
	/**
	 * <DIV class="ColorPreview" style="background:#FFD700;"></DIV> 
	 */
	const Gold = '#FFD700';
	/**
	 * <DIV class="ColorPreview" style="background:#DAA520;"></DIV> 
	 */
	const GoldenRod = '#DAA520';
	/**
	 * <DIV class="ColorPreview" style="background:#808080;"></DIV> 
	 */
	const Gray = '#808080';
	/**
	 * <DIV class="ColorPreview" style="background:#008000;"></DIV> 
	 */
	const Green = '#008000';
	/**
	 * <DIV class="ColorPreview" style="background:#ADFF2F;"></DIV> 
	 */
	const GreenYellow = '#ADFF2F';
	/**
	 * <DIV class="ColorPreview" style="background:#F0FFF0;"></DIV> 
	 */
	const HoneyDew = '#F0FFF0';
	/**
	 * <DIV class="ColorPreview" style="background:#FF69B4;"></DIV> 
	 */
	const HotPink = '#FF69B4';
	/**
	 * <DIV class="ColorPreview" style="background:#CD5C5C;"></DIV> 
	 */
	const IndianRed = '#CD5C5C';
	/**
	 * <DIV class="ColorPreview" style="background:#4B0082;"></DIV> 
	 */
	const Indigo = '#4B0082';
	/**
	 * <DIV class="ColorPreview" style="background:#FFFFF0;"></DIV> 
	 */
	const Ivory = '#FFFFF0';
	/**
	 * <DIV class="ColorPreview" style="background:#F0E68C;"></DIV> 
	 */
	const Khaki = '#F0E68C';
	/**
	 * <DIV class="ColorPreview" style="background:#E6E6FA;"></DIV> 
	 */
	const Lavender = '#E6E6FA';
	/**
	 * <DIV class="ColorPreview" style="background:#FFF0F5;"></DIV> 
	 */
	const LavenderBlush = '#FFF0F5';
	/**
	 * <DIV class="ColorPreview" style="background:#7CFC00;"></DIV> 
	 */
	const LawnGreen = '#7CFC00';
	/**
	 * <DIV class="ColorPreview" style="background:#FFFACD;"></DIV> 
	 */
	const LemonChiffon = '#FFFACD';
	/**
	 * <DIV class="ColorPreview" style="background:#ADD8E6;"></DIV> 
	 */
	const LightBlue = '#ADD8E6';
	/**
	 * <DIV class="ColorPreview" style="background:#F08080;"></DIV> 
	 */
	const LightCoral = '#F08080';
	/**
	 * <DIV class="ColorPreview" style="background:#E0FFFF;"></DIV> 
	 */
	const LightCyan = '#E0FFFF';
	/**
	 * <DIV class="ColorPreview" style="background:#FAFAD2;"></DIV> 
	 */
	const LightGoldenRodYellow = '#FAFAD2';
	/**
	 * <DIV class="ColorPreview" style="background:#90EE90;"></DIV> 
	 */
	const LightGreen = '#90EE90';
	/**
	 * <DIV class="ColorPreview" style="background:#D3D3D3;"></DIV> 
	 */
	const LightGrey = '#D3D3D3';
	/**
	 * <DIV class="ColorPreview" style="background:#FFB6C1;"></DIV> 
	 */
	const LightPink = '#FFB6C1';
	/**
	 * <DIV class="ColorPreview" style="background:#FFA07A;"></DIV> 
	 */
	const LightSalmon = '#FFA07A';
	/**
	 * <DIV class="ColorPreview" style="background:#20B2AA;"></DIV> 
	 */
	const LightSeaGreen = '#20B2AA';
	/**
	 * <DIV class="ColorPreview" style="background:#87CEFA;"></DIV> 
	 */
	const LightSkyBlue = '#87CEFA';
	/**
	 * <DIV class="ColorPreview" style="background:#778899;"></DIV> 
	 */
	const LightSlateGray = '#778899';
	/**
	 * <DIV class="ColorPreview" style="background:#B0C4DE;"></DIV> 
	 */
	const LightSteelBlue = '#B0C4DE';
	/**
	 * <DIV class="ColorPreview" style="background:#FFFFE0;"></DIV> 
	 */
	const LightYellow = '#FFFFE0';
	/**
	 * <DIV class="ColorPreview" style="background:#00FF00;"></DIV> 
	 */
	const Lime = '#00FF00';
	/**
	 * <DIV class="ColorPreview" style="background:#32CD32;"></DIV> 
	 */
	const LimeGreen = '#32CD32';
	/**
	 * <DIV class="ColorPreview" style="background:#FAF0E6;"></DIV> 
	 */
	const Linen = '#FAF0E6';
	/**
	 * <DIV class="ColorPreview" style="background:#FF00FF;"></DIV> 
	 */
	const Magenta = '#FF00FF';
	/**
	 * <DIV class="ColorPreview" style="background:#800000;"></DIV> 
	 */
	const Maroon = '#800000';
	/**
	 * <DIV class="ColorPreview" style="background:#66CDAA;"></DIV> 
	 */
	const MediumAquaMarine = '#66CDAA';
	/**
	 * <DIV class="ColorPreview" style="background:#0000CD;"></DIV> 
	 */
	const MediumBlue = '#0000CD';
	/**
	 * <DIV class="ColorPreview" style="background:#BA55D3;"></DIV> 
	 */
	const MediumOrchid = '#BA55D3';
	/**
	 * <DIV class="ColorPreview" style="background:#9370D8;"></DIV> 
	 */
	const MediumPurple = '#9370D8';
	/**
	 * <DIV class="ColorPreview" style="background:#3CB371;"></DIV> 
	 */
	const MediumSeaGreen = '#3CB371';
	/**
	 * <DIV class="ColorPreview" style="background:#7B68EEv;"></DIV> 
	 */
	const MediumSlateBlue = '#7B68EEv';
	/**
	 * <DIV class="ColorPreview" style="background:#00FA9A;"></DIV> 
	 */
	const MediumSpringGreen = '#00FA9A';
	/**
	 * <DIV class="ColorPreview" style="background:#48D1CC;"></DIV> 
	 */
	const MediumTurquoise = '#48D1CC';
	/**
	 * <DIV class="ColorPreview" style="background:#C71585;"></DIV> 
	 */
	const MediumVioletRed = '#C71585';
	/**
	 * <DIV class="ColorPreview" style="background:#191970;"></DIV> 
	 */
	const MidnightBlue = '#191970';
	/**
	 * <DIV class="ColorPreview" style="background:#F5FFFA;"></DIV> 
	 */
	const MintCream = '#F5FFFA';
	/**
	 * <DIV class="ColorPreview" style="background:#FFE4E1;"></DIV> 
	 */
	const MistyRose = '#FFE4E1';
	/**
	 * <DIV class="ColorPreview" style="background:#FFE4B5;"></DIV> 
	 */
	const Moccasin = '#FFE4B5';
	/**
	 * <DIV class="ColorPreview" style="background:#FFDEAD;"></DIV> 
	 */
	const NavajoWhite = '#FFDEAD';
	/**
	 * <DIV class="ColorPreview" style="background:#000080;"></DIV> 
	 */
	const Navy = '#000080';
	/**
	 * <DIV class="ColorPreview" style="background:#FDF5E6;"></DIV> 
	 */
	const OldLace = '#FDF5E6';
	/**
	 * <DIV class="ColorPreview" style="background:#808000;"></DIV> 
	 */
	const Olive = '#808000';
	/**
	 * <DIV class="ColorPreview" style="background:#6B8E23;"></DIV> 
	 */
	const OliveDrab = '#6B8E23';
	/**
	 * <DIV class="ColorPreview" style="background:#FFA500;"></DIV> 
	 */
	const Orange = '#FFA500';
	/**
	 * <DIV class="ColorPreview" style="background:#FF4500;"></DIV> 
	 */
	const OrangeRed = '#FF4500';
	/**
	 * <DIV class="ColorPreview" style="background:#DA70D6;"></DIV> 
	 */
	const Orchid = '#DA70D6';
	/**
	 * <DIV class="ColorPreview" style="background:#EEE8AA;"></DIV> 
	 */
	const PaleGoldenRod = '#EEE8AA';
	/**
	 * <DIV class="ColorPreview" style="background:#98FB98;"></DIV> 
	 */
	const PaleGreen = '#98FB98';
	/**
	 * <DIV class="ColorPreview" style="background:#AFEEEE;"></DIV> 
	 */
	const PaleTurquoise = '#AFEEEE';
	/**
	 * <DIV class="ColorPreview" style="background:#D87093;"></DIV> 
	 */
	const PaleVioletRed = '#D87093';
	/**
	 * <DIV class="ColorPreview" style="background:#FFEFD5;"></DIV> 
	 */
	const PapayaWhip = '#FFEFD5';
	/**
	 * <DIV class="ColorPreview" style="background:#FFDAB9;"></DIV> 
	 */
	const PeachPuff = '#FFDAB9';
	/**
	 * <DIV class="ColorPreview" style="background:#CD853F;"></DIV> 
	 */
	const Peru = '#CD853F';
	/**
	 * <DIV class="ColorPreview" style="background:#FFC0CB;"></DIV> 
	 */
	const Pink = '#FFC0CB';
	/**
	 * <DIV class="ColorPreview" style="background:#DDA0DD;"></DIV> 
	 */
	const Plum = '#DDA0DD';
	/**
	 * <DIV class="ColorPreview" style="background:#B0E0E6;"></DIV> 
	 */
	const PowderBlue = '#B0E0E6';
	/**
	 * <DIV class="ColorPreview" style="background:#800080;"></DIV> 
	 */
	const Purple = '#800080';
	/**
	 * <DIV class="ColorPreview" style="background:#FF0000;"></DIV> 
	 */
	const Red = '#FF0000';
	/**
	 * <DIV class="ColorPreview" style="background:#BC8F8F;"></DIV> 
	 */
	const RosyBrown = '#BC8F8F';
	/**
	 * <DIV class="ColorPreview" style="background:#4169E1;"></DIV> 
	 */
	const RoyalBlue = '#4169E1';
	/**
	 * <DIV class="ColorPreview" style="background:#8B4513;"></DIV> 
	 */
	const SaddleBrown = '#8B4513';
	/**
	 * <DIV class="ColorPreview" style="background:#FA8072;"></DIV> 
	 */
	const Salmon = '#FA8072';
	/**
	 * <DIV class="ColorPreview" style="background:#F4A460;"></DIV> 
	 */
	const SandyBrown = '#F4A460';
	/**
	 * <DIV class="ColorPreview" style="background:#2E8B57;"></DIV> 
	 */
	const SeaGreen = '#2E8B57';
	/**
	 * <DIV class="ColorPreview" style="background:#FFF5EE;"></DIV> 
	 */
	const SeaShell = '#FFF5EE';
	/**
	 * <DIV class="ColorPreview" style="background:#A0522D;"></DIV> 
	 */
	const Sienna = '#A0522D';
	/**
	 * <DIV class="ColorPreview" style="background:#C0C0C0;"></DIV> 
	 */
	const Silver = '#C0C0C0';
	/**
	 * <DIV class="ColorPreview" style="background:#87CEEB;"></DIV> 
	 */
	const SkyBlue = '#87CEEB';
	/**
	 * <DIV class="ColorPreview" style="background:#6A5ACD;"></DIV> 
	 */
	const SlateBlue = '#6A5ACD';
	/**
	 * <DIV class="ColorPreview" style="background:#708090;"></DIV> 
	 */
	const SlateGray = '#708090';
	/**
	 * <DIV class="ColorPreview" style="background:#FFFAFA;"></DIV> 
	 */
	const Snow = '#FFFAFA';
	/**
	 * <DIV class="ColorPreview" style="background:#00FF7F;"></DIV> 
	 */
	const SpringGreen = '#00FF7F';
	/**
	 * <DIV class="ColorPreview" style="background:#4682B4;"></DIV> 
	 */
	const SteelBlue = '#4682B4';
	/**
	 * <DIV class="ColorPreview" style="background:#D2B48C;"></DIV> 
	 */
	const Tan = '#D2B48C';
	/**
	 * <DIV class="ColorPreview" style="background:#008080;"></DIV> 
	 */
	const Teal = '#008080';
	/**
	 * <DIV class="ColorPreview" style="background:#D8BFD8;"></DIV> 
	 */
	const Thistle = '#D8BFD8';
	/**
	 * <DIV class="ColorPreview" style="background:#FF6347;"></DIV> 
	 */
	const Tomato = '#FF6347';
	/**
	 * <DIV class="ColorPreview" style="background:#40E0D0;"></DIV> 
	 */
	const Turquoise = '#40E0D0';
	/**
	 * <DIV class="ColorPreview" style="background:#EE82EE;"></DIV> 
	 */
	const Violet = '#EE82EE';
	/**
	 * <DIV class="ColorPreview" style="background:#F5DEB3;"></DIV> 
	 */
	const Wheat = '#F5DEB3';
	/**
	 * <DIV class="ColorPreview" style="background:#FFFFFF;"></DIV> 
	 */
	const White = '#FFFFFF';
	/**
	 * <DIV class="ColorPreview" style="background:#F5F5F5;"></DIV> 
	 */
	const WhiteSmoke = '#F5F5F5';
	/**
	 * <DIV class="ColorPreview" style="background:#FFFF00;"></DIV> 
	 */
	const Yellow = '#FFFF00';
	/**
	 * <DIV class="ColorPreview" style="background:#9ACD32;"></DIV> 
	 */
	const YellowGreen = '#9ACD32';
	
	private function Color(){}
	/**
	 * @ignore
	 */
	public static function ToVector($string)
	{
		return array(
			hexdec(substr($string, 1, 2)), 
			hexdec(substr($string, 3, 2)), 
			hexdec(substr($string, 5, 2))
		);
	}
	/**
	 * Returns a Color via a mix of red, green, and blue components
	 * @param integer|string $red
	 * @param integer|string $green
	 * @param integer|string $blue
	 * @return Color
	 */
	public static function RGB($red, $green, $blue)
	{
		return '#' .
			(is_int($red) ? ($red <= 15 ? '0'.dechex($red) : dechex($red)) : $red) .
			(is_int($green) ? ($green <= 15 ? '0'.dechex($green) : dechex($green)) : $green) .
			(is_int($blue) ? ($blue <= 15 ? '0'.dechex($blue) : dechex($blue)) : $blue);
	}
	/**
	 * Returns a Color via a mix of any two existing Colors
	 * @param Color $color1
	 * @param Color $color2
	 * @param positive_float $ratio
	 * @return Color
	 */
	public static function Mix($color1, $color2, $ratio=1)
	{
		$v1 = self::ToVector($color1);
		$v2 = self::ToVector($color2);
		++$ratio;
		return self::RGB(
			intval(($v1[0] + $v2[0]) / $ratio),
			intval(($v1[1] + $v2[1]) / $ratio),
			intval(($v1[2] + $v2[2]) / $ratio)
		);
	}
	/**
	 * Returns a Color by darkening an existing color by some percent. 
	 * For example, the following makes $color one and a half times as dark:
	 * <pre>Color::Darken($color, 50);</pre>
	 * @param Color $color
	 * @param positive_float $percent
	 * @return Color
	 */
	public static function Darken($color, $percent = 100)
	{
		return self::Mix($color, self::Black, $percent/100);
	}
	/**
	 * Returns a Color by lightening an existing color by some percent. 
	 * For example, the following makes $color one and a half times as light:
	 * <pre>Color::Lighten($color, 50);</pre>
	 * @param Color $color
	 * @param positive_float $percent
	 * @return Color
	 */
	public static function Lighten($color, $percent = 100)
	{
		return self::Mix($color, self::White, $percent/100);
	}
}

?>