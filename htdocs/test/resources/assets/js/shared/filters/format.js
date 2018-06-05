import moment from 'moment';
import * as dateFormats from '_shared/lib/dateFormats';

export default (date, format) => {
  if (date == null) {
    return null;
  }

  let dateFormat = format;
  if (format == null) {
    dateFormat = 'db';
  }

  if (dateFormats[dateFormat]) {
    dateFormat = dateFormats[dateFormat];
  }
  return moment(date).format(dateFormat);
};
