# Calculator Idea

A short design note for the `calculator-idea` project.

## Overview

The goal of this repository is to build a small, fast, and accessible calculator.
It should feel instant on the keyboard and read clearly on screen, without
getting in the way of the person using it.

## Goals

- Correct arithmetic for everyday use (add, subtract, multiply, divide).
- Predictable behavior for edge cases (division by zero, long numbers, rounding).
- Keyboard-first input, with mouse and touch as secondary paths.
- No tracking, no network calls, no dependencies required to run.

## Non-Goals

- Scientific/graphing functions in the first version.
- Multi-user accounts or cloud sync.
- Custom themes beyond a light/dark pair.

## Proposed Feature Set

| Feature        | Priority | Notes                                        |
| -------------- | -------- | -------------------------------------------- |
| Basic ops      | Must     | `+`, `-`, `×`, `÷`                            |
| Decimal input  | Must     | Single decimal separator, locale-aware output |
| Percent        | Should   | Percentage of a value, not just `/100`        |
| Memory slots   | Should   | Store, recall, clear                          |
| History tape   | Could    | Scrollable list of past calculations          |
| Keyboard hints | Could    | Inline display of available shortcuts         |

## Behavior Notes

- **Division by zero** returns a visible error state rather than `Infinity` or
  `NaN`; the error clears as soon as new input arrives.
- **Precision** uses decimal-safe arithmetic so `0.1 + 0.2` displays as `0.3`.
- **Chained input** applies operations left to right, matching a physical
  calculator rather than full operator precedence.
- **Overflow** switches to exponential notation instead of truncating digits.

## Accessibility

- Every control is reachable and operable by keyboard alone.
- Results are announced through a live region for screen readers.
- Contrast meets WCAG AA in both light and dark themes.

## Roadmap

1. Write down the arithmetic rules and edge cases (this document).
2. Build a minimal working calculator with basic operations.
3. Add percent, memory, and the history tape.
4. Polish accessibility, themes, and keyboard shortcuts.
5. Add tests around precision and error states.

## Contributing

Open an issue for ideas or bugs. Pull requests are welcome — keep changes small,
describe the behavior being changed, and add a test when fixing a bug.
