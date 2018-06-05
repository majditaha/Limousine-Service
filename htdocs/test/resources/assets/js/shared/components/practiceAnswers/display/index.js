import SingleChoice from '_components/practiceAnswers/display/singleChoice';
import MultipleChoice from '_components/practiceAnswers/display/multipleChoice';
import SingleValue from '_components/practiceAnswers/display/singleValue';
import MultipleValue from '_components/practiceAnswers/display/multipleValue';
import TextValue from '_components/practiceAnswers/display/textValue';
import Matching from '_components/practiceAnswers/display/matching';

export default {

  functional: true,

  // Value (v-model) should be an object with format { answerId: userGivenValue }
  render(h, context) {
    switch (context.props.type) {
      case 'single_choice':
        return h(SingleChoice, context.data);
      case 'multiple_choice':
        return h(MultipleChoice, context.data);
      case 'single_value':
        return h(SingleValue, context.data);
      case 'multiple_value':
        return h(MultipleValue, context.data);
      case 'text':
        return h(TextValue, context.data);
      case 'matching':
        return h(Matching, context.data);
      default:
        return null;
    }
  },

};
