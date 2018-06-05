import OptionsSelector from '_components/practiceAnswers/editor/optionsSelector';
import SingleValue from '_components/practiceAnswers/editor/singleValue';
import TextValue from '_components/practiceAnswers/editor/textValue';
import Matching from '_components/practiceAnswers/editor/matching';

export default {

  functional: true,

  render(h, context) {
    switch (context.props.type) {
      case 'single_choice':
        return h(OptionsSelector, context.data);
      case 'multiple_choice':
        return h(OptionsSelector, context.data);
      case 'single_value':
        return h(SingleValue, context.data);
      case 'multiple_value':
        // eslint-disable-next-line no-param-reassign
        context.data.attrs.showCorrect = false;
        return h(OptionsSelector, context.data);
      case 'text':
        return h(TextValue);
      case 'matching':
        return h(Matching, context.data);
      default:
        return null;
    }
  },

};
