<?php declare(strict_types=1);

return [
	'single-line single space' => [
		'""" """',
		' ',
	],
	'multi-line no content' => [
		'"""
"""',
		'',
	],
	'single-line single letter' => [
		'"""A"""',
		'A',
	],
	'multi-line single letter no indent' => [
		'
"""
A
"""
		',
		'A',
	],
	'multi-line single letter single space indent' => [
		'
"""
 A
"""
		',
		'A',
	],
	'multi-line single letter single tab indent' => [
		'
"""
	A
"""
		',
		'A',
	],
	'multiple single-letter lines no indent' => [
		'
"""
A
B
C
"""
		',
		'A
B
C',
	],
	'multiple single-letter lines single space indent' => [
		'
"""
 A
 B
 C
"""
		',
		'A
B
C',
	],
	'multiple single-letter lines single tab indent' => [
		'
"""
	A
	B
	C
"""
		',
		'A
B
C',
	],
	'multiple single-letter lines uneven space indent ascending' => [
		'
"""
A
 B
  C
"""
		',
		'A
 B
  C',
	],
	'multiple single-letter lines uneven space indent descending' => [
		'
"""
  A
 B
C
"""
		',
		'  A
 B
C',
	],
	'multiple single-letter lines uneven space indent random' => [
		'
"""
  A
    B
 C
"""
		',
		' A
   B
C',
	],
	'multiple single-letter lines uneven tab indent ascending' => [
		'
"""
A
	B
		C
"""
		',
		'A
	B
		C',
	],
	'multiple single-letter lines uneven tab indent descending' => [
		'
"""
		A
	B
C
"""
		',
		'		A
	B
C',
	],
	'multiple single-letter lines uneven tab indent random' => [
		'
"""
		A
				B
	C
"""
		',
		'	A
			B
C',
	],
];
