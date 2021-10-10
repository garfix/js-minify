# Implementation

This library is fast because it uses regular expressions to find and replace pieces of Javascript code. These regexps are implemented using optimized C code.

## Definitions

* A "word" in this class is any scalar value, variable, or keyword. Two words may not be glued together; whitespace between them is required.
* A "block" in this class is a piece of Javascript code that contains whitespace that may not be removed.

## Main algorithm

The algorithm has four steps. The whitespace is removed in step 3, in a single sweep.
Steps 1 and 2 prepare for this step. Step 4 cleans up after.

Step 1: remove comments

* All comments that may be removed, are removed
* By removing comments, it is much easier to reason about whitespace, in step 2, because the whitespace is no longer polluted by comments.

Step 2: handle all blocks

* Match the blocks (pieces of code that contain required whitespace)
* Leave the blocks in the Javascript, but replace their whitespace by placeholders
* All whitespace that is now left is optional and can be removed

Step 3: remove all remaining whitespace

* At this point all whitespace that is left can be safely and quickly removed
* Special caution is taken for newlines; they are only removed if it is certain that the semantics of the code is not changed.

Step 4: replace placeholders by whitespace

* Replace the placeholders that were created in step 2 and replace them by their whitespace

## About Javascript regular expressions

Javascript has a special syntactic construct for regular expression: /abc(d)ef/i
Care has been taken that this construct is not confused with

* single line comments, which look like an empty regexp: //
* a combination of two divisions: 1 / (x + y) / z

When a regexp is followed by a newline, this newline may not be replaced by a simple space.

## About the plus and minus operators

When the space between an ++ operator and a + operator is removed, a syntax error occurs: a + ++b -> a+++b

So some sort of whitespace must be preserved.

## About the use of regular expressions in this library

This library can be fast because it uses regular expressions quite a bit.

* The code supports both ASCII and UTF-8 Javascript.
* All regular expressions have the unicode modifier (u), which ensures that the JS is not treated as bytes but as encoded code points. 
* The "dotall" modifier (s) is used everywhere: the dot (.) also matches the newline
* The expressions use the greedy quantifier after or-groups, to avoid the chance of JIT stack overflow.
