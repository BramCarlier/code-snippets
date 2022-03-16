const defaultConfig = require('tailwindcss/defaultConfig');

module.exports = {
  ...defaultConfig,
  important: true,
  theme: {
    ...defaultConfig.theme,
    colors: {
      ...defaultConfig.theme.colors,

      inherit: 'inherit',

      'status-green': '#B6E254',
      'status-red': '#FC4F4F',
      'status-orange': '#FFE07B',
      'status-blue': '#6EBCFF',

      'notice-green': '#DCFADD',

      'success': '#DCFADD',
      'success-border': '#B8EABB',
      'success-text': '#156F1A',

      'error': '#fda7a7',
      'error-border': '#FC4F4F',
      'error-text': '#B03737',

      'warning': '#FFE07B',
      'warning-border': '#FFCC33',
      'warning-text': '#FFB902',

      'linked-button': '#3E9643',
      'linked-button-light': '#56bf5c',
      'linked': '#DCFADD',
      'linked-border': '#B8EABB',

      'correct': '#9BCC2E',
      'wrong': '#FC4F4F',

      primary: {
        100: '#FFCC33', //lightest
        200: '#FCD9B6', //lighter
        300: '#FFE07B', //light
        400: '#FFEAA5', //light-alternative
        500: '#FFF9E8', //light-hover
        600: '#FFCC33', //primary
        700: '#FFB902', //dark
        800: '#613B1F', //darker
        900: '#462A16', //darkest
      },
      secondary: {
        100: '#EAF5FF', //lightest
        200: '#EAF5FF', //lighter
        300: '#6EBCFF', //light
        400: '#90cdf4', //light-hover
        500: '#90cdf4', //light-alternative
        600: '#55B1FF', //secondary
        700: '#55B1FF', //dark
        800: '#55B1FF', //darker
        900: '#55B1FF', //darkest
      },
      gray: {
        100: '#F7F7F7', //light && background
        200: '#EAEAEA', //mild && menu
        300: '#C4C4C4', //mild-dark
        400: '#A6A6A8', //alternative
        500: '#56565B', //dark
        600: '#718096',
        700: '#4a5568',
        800: '#2d3748',
        900: '#1a202c',
      }
    },
    spacing: {
      ...defaultConfig.theme.spacing,
      '1.5': '0.3rem',
      '2.5': '0.6rem',
      '5.5': '1.4rem',
      '7': '1.8rem',
      '9': '2.25rem',
      '15': '3.75rem',
      '23': '5.75rem',
      '36': '9.5rem',
      'btn': '8.2%',
      '10%': '10%',
      '5px': '5px',
      '40px': '40px',
    },
    borderWidth: {
      ...defaultConfig.theme.borderWidth,
      '1.5': '1.5px',
      '3': '3px',
      '15': '15px'
    },
    fontFamily: {
      ...defaultConfig.theme.fontFamily,
      primary: ['Cera Basic'],
      'primary-bold': ['Cera Basic Bold'],
      secondary: ['Roboto'],
      'secondary-bold': ['Roboto Bold'],
      awesome: ['FontAwesome'],
      base: ['Base'],
    },
    lineHeight: {
      ...defaultConfig.theme.lineHeight,
      '2.5': '2.5',
    },
    maxWidth: {
      ...defaultConfig.theme.maxWidth,
      '1/4': '25%',
      '1/2': '50%',
      '3/4': '75%',
      '350px': '350px',
    },
    minWidth: {
      ...defaultConfig.theme.minWidth,
      '165px': '165px',
      '200px': '200px',
      '350px': '350px',
    },
    zIndex: {
      ...defaultConfig.theme.zIndex,
      '100': 100,
      '999': 999,
      'top': 9999
    },
  },
  variants: {
    ...defaultConfig.variants,
    opacity: [...defaultConfig.variants.opacity, 'group-hover'],
    textColor: [...defaultConfig.variants.textColor, 'group-hover']
  },
};
