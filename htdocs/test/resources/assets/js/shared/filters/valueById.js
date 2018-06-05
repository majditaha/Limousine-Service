import _ from 'lodash/fp';

export default function (id, array, column = 'name') {
  const found = _.find(['id', id])(array);
  return (found && [column] in found) ? found[column] : null;
}
