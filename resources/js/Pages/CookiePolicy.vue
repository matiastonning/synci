<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Footer from "./Components/Footer.vue";
import Navbar from "./Components/Navbar.vue";
import { CheckCircleIcon, InformationCircleIcon } from '@heroicons/vue/20/solid'


defineProps({
    policy: String,
});
</script>

<template>
    <Head title="Cookie Policy" />

    <Navbar />

    <div class="py-32 px-6 lg:px-8">
        <div class="mx-auto max-w-5xl text-base leading-7 text-gray-200">
            <p class="text-base font-semibold leading-7 text-teal-500 dark:text-teal-400">Legal</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl">Cookie Policy</h1>
            <p class="mt-6 text-xl leading-8 dark:text-gray-300 text-gray-600">We use cookies to help improve your experience of our website at https://synci.io. This cookie policy is part of Synci.io's privacy policy. It covers the use of cookies between your device and our site.<br/><br/>
                We also provide basic information on third-party services we may use, who may also use cookies as part of their service. This policy does not cover their cookies.
            </p>
            <div class="mt-10 max-w-2xl">
                <p class="dark:text-gray-500 text-gray-400">If you don’t wish to accept cookies from us, you should instruct your browser to refuse cookies from https://synci.io. In such a case, we may be unable to provide you with some of your desired content and services.</p>
            </div>
            <div class="mt-16 max-w-2xl">
                <h3 class="text-xl mt-16 font-bold tracking-tight text-gray-900 text-gray-900 dark:text-gray-200">What is a cookie?</h3>
                <p class="mt-6 dark:text-gray-300 text-gray-600">A cookie is a small piece of data that a website stores on your device when you visit. It typically contains information about the website itself, a unique identifier that allows the site to recognize your web browser when you return, additional data that serves the cookie’s purpose, and the lifespan of the cookie itself.</p>
                <p class="mt-8 dark:text-gray-300 text-gray-600">Cookies are used to enable certain features (e.g. logging in), track site usage (e.g. analytics), store your user settings (e.g. time zone, notification preferences), and to personalize your content (e.g. advertising, language).</p>
                <p class="mt-8 dark:text-gray-300 text-gray-600">Cookies set by the website you are visiting are usually referred to as first-party cookies. They typically only track your activity on that particular site.</p>
                <p class="mt-8 dark:text-gray-300 text-gray-600">Cookies set by other sites and companies (i.e. third parties) are called third-party cookies. They can be used to track you on other websites that use the same third-party service.</p>

                <h3 class="text-xl mt-16 font-bold tracking-tight text-gray-900 text-gray-900 dark:text-gray-200">Liability</h3>
                <p class="mt-6 dark:text-gray-300 text-gray-600">Our website and the materials on our website are provided on an 'as is' basis. To the extent permitted by law, Synci.io makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property, or other violation of rights.</p>
                <p class="mt-8 dark:text-gray-300 text-gray-600">In no event shall Synci.io or its suppliers be liable for any consequential loss suffered or incurred by you or any third party arising from the use or inability to use this website or the materials on this website, even if Synci.io or an authorized representative has been notified, orally or in writing, of the possibility of such damage.</p>
                <p class="mt-8 dark:text-gray-300 text-gray-600">In the context of this agreement, “consequential loss” includes any consequential loss, indirect loss, real or anticipated loss of profit, loss of benefit, loss of revenue, loss of business, loss of goodwill, loss of opportunity, loss of savings, loss of reputation, loss of use and/or loss or corruption of data, whether under statute, contract, equity, tort (including negligence), indemnity, or otherwise.</p>
                <p class="mt-8 dark:text-gray-300 text-gray-600">Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.</p>

                <h3 class="text-xl mt-16 font-bold tracking-tight text-gray-900 dark:text-gray-200">Types of cookies and how we use them</h3>

                <h4 class="text-md mt-16 font-bold tracking-tight text-gray-900 text-gray-900 dark:text-gray-200">Essential cookies</h4>
                <p class="mt-6 dark:text-gray-300 text-gray-600">Essential cookies are crucial to your experience of a website, enabling core features like user logins, account management, shopping carts, and payment processing.</p>
                <p class="mt-8 dark:text-gray-300 text-gray-600">We use essential cookies to enable certain functions on our website.</p>

                <h4 class="text-md mt-16 font-bold tracking-tight text-gray-900 text-gray-900 dark:text-gray-200">Performance cookies</h4>
                <p class="mt-6 dark:text-gray-300 text-gray-600">Performance cookies track how you use a website during your visit. Typically, this information is anonymous and aggregated, with information tracked across all site users. They help companies understand visitor usage patterns, identify and diagnose problems or errors their users may encounter, and make better strategic decisions in improving their audience’s overall website experience. These cookies may be set by the website you’re visiting (first-party) or by third-party services. They do not collect personal information about you.</p>
                <p class="mt-8 dark:text-gray-300 text-gray-600">We use performance cookies on our site.</p>

                <h4 class="text-md mt-16 font-bold tracking-tight text-gray-900 text-gray-900 dark:text-gray-200">Functionality cookies</h4>
                <p class="mt-6 dark:text-gray-300 text-gray-600">Functionality cookies are used to collect information about your device and any settings you may configure on the website you’re visiting (like language and time zone settings). With this information, websites can provide you with customized, enhanced, or optimized content and services. These cookies may be set by the website you’re visiting (first-party) or by third-party services.</p>
                <p class="mt-8 dark:text-gray-300 text-gray-600">We use functionality cookies on our site.</p>

                <h4 class="text-md mt-16 font-bold tracking-tight text-gray-900 text-gray-900 dark:text-gray-200">Targeting/advertising cookies</h4>
                <p class="mt-6 dark:text-gray-300 text-gray-600">Targeting/advertising cookies help determine what promotional content is most relevant and appropriate to you and your interests. Websites may use them to deliver targeted advertising or limit the number of times you see an advertisement. This helps companies improve the effectiveness of their campaigns and the quality of content presented to you. These cookies may be set by the website you’re visiting (first-party) or by third-party services. Targeting/advertising cookies set by third-parties may be used to track you on other websites that use the same third-party service.</p>
                <p class="mt-8 dark:text-gray-300 text-gray-600">We do not use this type of cookie on our site.</p>
            </div>
        </div>
    </div>



    <!-- Footer -->
    <Footer />
</template>
