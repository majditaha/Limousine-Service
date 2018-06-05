export default (number, one, two, five, includeNum = true) => {
  let n = Math.abs(number);
  n %= 100;
  if (n >= 5 && n <= 20) {
    return includeNum ? `${number} ${five}` : five;
  }
  n %= 10;
  if (n === 1) {
    return includeNum ? `${number} ${one}` : one;
  }
  if (n >= 2 && n <= 4) {
    return includeNum ? `${number} ${two}` : two;
  }
  return includeNum ? `${number} ${five}` : five;
};
