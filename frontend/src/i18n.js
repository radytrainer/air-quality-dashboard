import { createI18n } from 'vue-i18n'

const messages = {
  en: {
    nav: {
      home: 'Home',
      cityDetail: 'City Detail',
      compareCities: 'Compare Cities',
      analytics: 'Analytics',
      dashboard: 'Dashboard',
      healthAlert: 'Health Alert',
      cityAQI: 'City AQI',
    },
    auth: {
      login: 'Login',
      logout: 'Logout',
      loginTitle: 'Login to Planet Air Check',
      email: 'Email',
      password: 'Password',
      emailPlaceholder: 'Email',
      passwordPlaceholder: 'Password',
      loggingIn: 'Logging in...',
      dontHaveAccount: 'Don’t have an account?',
      register: 'Register',
      registerTitle: 'Create an Account',
      name: 'Full Name',
      namePlaceholder: 'Full Name',
      registering: 'Registering...',
      registerFailed: 'Registration failed. Please try again.',
      loginFailed: 'Login failed. Please check your credentials.',
    },
  },
  kh: {
    nav: {
      home: 'ទំព័រដើម',
      cityDetail: 'ព័ត៌មានអំពីទីក្រុង',
      compareCities: 'ប្រៀបធៀបទីក្រុង',
      analytics: 'វិភាគទិន្នន័យ',
      dashboard: 'ផ្ទាំងគ្រប់គ្រង',
      healthAlert: 'ការព្រមានសុខភាព',
      cityAQI: 'ព័ត៌មានអាកាសនៃទីក្រុង',
      
    },
    auth: {
      login: 'ចូលគណនី',
      logout: 'ចាកចេញ',
      loginTitle: 'ចូលទៅកាន់ Planet Air Check',
      email: 'អ៊ីមែល',
      password: 'ពាក្យសម្ងាត់',
      emailPlaceholder: 'អ៊ីមែល',
      passwordPlaceholder: 'ពាក្យសម្ងាត់',
      loggingIn: 'កំពុងចូល...',
      dontHaveAccount: 'មិនទាន់មានគណនី?',
      register: 'ចុះឈ្មោះ',
      registerTitle: 'បង្កើតគណនីថ្មី',
      name: 'ឈ្មោះពេញ',
      namePlaceholder: 'ឈ្មោះពេញ',
      registering: 'កំពុងចុះឈ្មោះ...',
      registerFailed: 'ការចុះឈ្មោះបរាជ័យ។ សូមសាកល្បងម្ដងទៀត។',
      loginFailed: 'ការចូលបរាជ័យ។ សូមពិនិត្យអ៊ីមែល និងពាក្យសម្ងាត់របស់អ្នក។',
    },
  },
}

const i18n = createI18n({
  locale: localStorage.getItem('lang') || 'en',
  fallbackLocale: 'en',
  messages,
})

export default i18n
