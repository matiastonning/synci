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
    <Head title="Terms of Service" />

    <Navbar />

    <div class="py-32 px-6 lg:px-8">
        <div class="mx-auto max-w-5xl text-base leading-7 text-gray-200">
            <p class="text-base font-semibold leading-7 text-teal-500 dark:text-teal-400">Legal</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl">Terms of Service</h1>
            <p class="mt-6 text-xl leading-8 dark:text-gray-300 text-gray-600">These Terms of Service govern your use of the website located at https://synci.io and any related services provided by Synci.io.<br/><br/>
                These Terms of Service were last updated on 12 March 2023.
            </p>
            <div class="mt-10 max-w-2xl">
                <p class="dark:text-gray-500 text-gray-400">By accessing https://synci.io, you agree to abide by these Terms of Service and to comply with all applicable laws and regulations. If you do not agree with these Terms of Service, you are prohibited from using or accessing this website or using any other services provided by Synci.io.<br/><br/>
                    We, Synci.io, reserve the right to review and amend any of these Terms of Service at our sole discretion. Upon doing so, we will update this page. Any changes to these Terms of Service will take effect immediately from the date of publication.</p>
            </div>
            <div class="mt-16 max-w-2xl">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-200">Limitations of Use</h2>
                <p class="mt-6 dark:text-gray-300 text-gray-600">By using this website, you warrant on behalf of yourself, your users, and other parties you represent that you will not:</p>
                <ol role="list" class="mt-8 max-w-xl space-y-8 dark:text-gray-400 text-gray-500">
                    <li class="flex gap-x-3">
                        <span class="text-gray-700 dark:text-gray-200">1. modify, copy, prepare derivative works of, decompile, or reverse engineer any materials and software contained on this website;</span>
                    </li>
                    <li class="flex gap-x-3">
                        <span class="text-gray-700 dark:text-gray-200">2. remove any copyright or other proprietary notations from any materials and software on this website;</span>
                    </li>
                    <li class="flex gap-x-3">
                        <span class="text-gray-700 dark:text-gray-200">3. transfer the materials to another person or “mirror” the materials on any other server;</span>
                    </li>
                    <li class="flex gap-x-3">
                        <span class="text-gray-700 dark:text-gray-200">4. knowingly or negligently use this website or any of its associated services in a way that abuses or disrupts our networks or any other service Synci.io provides;</span>
                    </li>
                    <li class="flex gap-x-3">
                        <span class="text-gray-700 dark:text-gray-200">5. use this website or its associated services to transmit or publish any harassing, indecent, obscene, fraudulent, or unlawful material;</span>
                    </li>
                    <li class="flex gap-x-3">
                        <span class="text-gray-700 dark:text-gray-200">6. use this website or its associated services in violation of any applicable laws or regulations;</span>
                    </li>
                    <li class="flex gap-x-3">
                        <span class="text-gray-700 dark:text-gray-200">7. use this website in conjunction with sending unauthorized advertising or spam;</span>
                    </li>
                    <li class="flex gap-x-3">
                        <span class="text-gray-700 dark:text-gray-200">8. harvest, collect, or gather user data without the user’s consent; or</span>
                    </li>
                    <li class="flex gap-x-3">
                        <span class="text-gray-700 dark:text-gray-200">9. use this website or its associated services in such a way that may infringe the privacy, intellectual property rights, or other rights of third parties.</span>
                    </li>
                </ol>

                <h3 class="text-xl mt-16 font-bold tracking-tight text-gray-900 text-gray-900 dark:text-gray-200">Intellectual Property</h3>
                <p class="mt-6 dark:text-gray-300 text-gray-600">The intellectual property in the materials contained in this website are owned by or licensed to Synci.io and are protected by applicable copyright and trademark law. We grant our users permission to download one copy of the materials for personal, non-commercial transitory use.</p>
                <p class="mt-8 dark:text-gray-300 text-gray-600">This constitutes the grant of a license, not a transfer of title. This license shall automatically terminate if you violate any of these restrictions or the Terms of Service, and may be terminated by Synci.io at any time.</p>

                <h3 class="text-xl mt-16 font-bold tracking-tight text-gray-900 text-gray-900 dark:text-gray-200">Liability</h3>
                <p class="mt-6 dark:text-gray-300 text-gray-600">Our website and the materials on our website are provided on an 'as is' basis. To the extent permitted by law, Synci.io makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property, or other violation of rights.</p>
                <p class="mt-8 dark:text-gray-300 text-gray-600">In no event shall Synci.io or its suppliers be liable for any consequential loss suffered or incurred by you or any third party arising from the use or inability to use this website or the materials on this website, even if Synci.io or an authorized representative has been notified, orally or in writing, of the possibility of such damage.</p>
                <p class="mt-8 dark:text-gray-300 text-gray-600">In the context of this agreement, “consequential loss” includes any consequential loss, indirect loss, real or anticipated loss of profit, loss of benefit, loss of revenue, loss of business, loss of goodwill, loss of opportunity, loss of savings, loss of reputation, loss of use and/or loss or corruption of data, whether under statute, contract, equity, tort (including negligence), indemnity, or otherwise.</p>
                <p class="mt-8 dark:text-gray-300 text-gray-600">Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.</p>

                <h3 class="text-xl mt-16 font-bold tracking-tight text-gray-900 dark:text-gray-200">Accuracy of Materials</h3>
                <p class="mt-6 dark:text-gray-300 text-gray-600">The materials appearing on our website are not comprehensive and are for general information purposes only. Synci.io does not warrant or make any representations concerning the accuracy, likely results, or reliability of the use of the materials on this website, or otherwise relating to such materials or on any resources linked to this website.</p>

                <h3 class="text-xl mt-16 font-bold tracking-tight text-gray-900 text-gray-900 dark:text-gray-200">Links</h3>
                <p class="mt-6 dark:text-gray-300 text-gray-600">Synci.io has not reviewed all of the sites linked to its website and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement, approval, or control by Synci.io of the site. Use of any such linked site is at your own risk and we strongly advise you make your own investigations with respect to the suitability of those sites.</p>

                <h3 class="text-xl mt-16 font-bold tracking-tight text-gray-900 text-gray-900 dark:text-gray-200">Right to Terminate</h3>
                <p class="mt-6 dark:text-gray-300 text-gray-600">We may suspend or terminate your right to use our website and terminate these Terms of Service immediately upon written notice to you for any breach of these Terms of Service.</p>

                <h3 class="text-xl mt-16 font-bold tracking-tight text-gray-900 text-gray-900 dark:text-gray-200">Severance</h3>
                <p class="mt-6 dark:text-gray-300 text-gray-600">Any term of these Terms of Service which is wholly or partially void or unenforceable is severed to the extent that it is void or unenforceable. The validity of the remainder of these Terms of Service is not affected.</p>

                <h3 class="text-xl mt-16 font-bold tracking-tight text-gray-900 text-gray-900 dark:text-gray-200">Governing Law</h3>
                <p class="mt-6 dark:text-gray-300 text-gray-600">These Terms of Service are governed by and construed in accordance with the laws of Norway. You irrevocably submit to the exclusive jurisdiction of the courts in that country.</p>
            </div>
        </div>
    </div>



    <!-- Footer -->
    <Footer />
</template>
