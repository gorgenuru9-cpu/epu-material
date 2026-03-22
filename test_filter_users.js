/**
 * Test suite for filterUsers() function
 * Tests combined filter logic with AND operation (Requirement 7.6)
 */

// Mock DOM elements
class MockElement {
    constructor(value = '') {
        this.value = value;
        this.dataset = {};
        this.style = { display: 'block' };
    }
}

class MockDocument {
    constructor() {
        this.elements = {
            searchUser: new MockElement(),
            filterDept: new MockElement(),
            filterStatus: new MockElement()
        };
        
        this.userCards = [
            this.createCard('john_doe', 'John Doe', 'requester', 'active'),
            this.createCard('jane_smith', 'Jane Smith', 'it_admin', 'active'),
            this.createCard('bob_jones', 'Bob Jones', 'treasury', 'inactive'),
            this.createCard('alice_wilson', 'Alice Wilson', 'requester', 'inactive'),
            this.createCard('charlie_brown', 'Charlie Brown', 'it_admin', 'active')
        ];
    }
    
    createCard(username, fullname, dept, status) {
        const card = new MockElement();
        card.dataset = { username, fullname, dept, status };
        return card;
    }
    
    getElementById(id) {
        return this.elements[id];
    }
    
    querySelectorAll(selector) {
        if (selector === '.user-card') {
            return this.userCards;
        }
        return [];
    }
}

// Create mock document
const document = new MockDocument();

/**
 * Filter users by search term, department, and activity status
 * Implements combined filter logic with AND operation (Requirement 7.6)
 */
function filterUsers() {
    const searchTerm = document.getElementById('searchUser').value.toLowerCase();
    const deptFilter = document.getElementById('filterDept').value;
    const statusFilter = document.getElementById('filterStatus').value;
    
    const userCards = document.querySelectorAll('.user-card');
    
    userCards.forEach(card => {
        const username = card.dataset.username.toLowerCase();
        const fullname = card.dataset.fullname.toLowerCase();
        const dept = card.dataset.dept;
        const status = card.dataset.status;
        
        // Check each filter criterion
        const matchesSearch = username.includes(searchTerm) || fullname.includes(searchTerm);
        const matchesDept = !deptFilter || dept === deptFilter;
        const matchesStatus = !statusFilter || status === statusFilter;
        
        // Show card only if ALL criteria match (AND operation)
        if (matchesSearch && matchesDept && matchesStatus) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Test utilities
function resetFilters() {
    document.elements.searchUser.value = '';
    document.elements.filterDept.value = '';
    document.elements.filterStatus.value = '';
    document.userCards.forEach(card => card.style.display = 'block');
}

function countVisibleCards() {
    return document.userCards.filter(card => card.style.display !== 'none').length;
}

function runTest(name, setup, expected) {
    resetFilters();
    setup();
    filterUsers();
    const actual = countVisibleCards();
    const pass = actual === expected;
    console.log(`${pass ? '✅' : '❌'} ${name}: Expected ${expected}, Got ${actual}`);
    return pass;
}

// Run all tests
console.log('Testing filterUsers() function with combined filters (Requirement 7.6)\n');

const results = [];

results.push(runTest(
    'Test 1: No filters - all users visible',
    () => {},
    5
));

results.push(runTest(
    'Test 2: Search only - "john"',
    () => { document.elements.searchUser.value = 'john'; },
    1
));

results.push(runTest(
    'Test 3: Department only - "requester"',
    () => { document.elements.filterDept.value = 'requester'; },
    2
));

results.push(runTest(
    'Test 4: Status only - "active"',
    () => { document.elements.filterStatus.value = 'active'; },
    3
));

results.push(runTest(
    'Test 5: Combined - search "smith" + department "it_admin"',
    () => {
        document.elements.searchUser.value = 'smith';
        document.elements.filterDept.value = 'it_admin';
    },
    1
));

results.push(runTest(
    'Test 6: Combined - department "requester" + status "active"',
    () => {
        document.elements.filterDept.value = 'requester';
        document.elements.filterStatus.value = 'active';
    },
    1
));

results.push(runTest(
    'Test 7: Combined - all three filters',
    () => {
        document.elements.searchUser.value = 'charlie';
        document.elements.filterDept.value = 'it_admin';
        document.elements.filterStatus.value = 'active';
    },
    1
));

results.push(runTest(
    'Test 8: Combined - no matches',
    () => {
        document.elements.searchUser.value = 'john';
        document.elements.filterDept.value = 'treasury';
    },
    0
));

results.push(runTest(
    'Test 9: Case insensitive search',
    () => { document.elements.searchUser.value = 'ALICE'; },
    1
));

results.push(runTest(
    'Test 10: Search by full name',
    () => { document.elements.searchUser.value = 'wilson'; },
    1
));

results.push(runTest(
    'Test 11: Partial username match',
    () => { document.elements.searchUser.value = 'bob'; },
    1
));

results.push(runTest(
    'Test 12: Combined - department "it_admin" + status "inactive"',
    () => {
        document.elements.filterDept.value = 'it_admin';
        document.elements.filterStatus.value = 'inactive';
    },
    0
));

results.push(runTest(
    'Test 13: Empty search with filters',
    () => {
        document.elements.searchUser.value = '';
        document.elements.filterDept.value = 'treasury';
        document.elements.filterStatus.value = 'inactive';
    },
    1
));

// Summary
const passed = results.filter(r => r).length;
const total = results.length;
console.log(`\n${'='.repeat(50)}`);
console.log(`Test Results: ${passed}/${total} passed`);
console.log(`${'='.repeat(50)}`);

if (passed === total) {
    console.log('✅ All tests passed! The filterUsers() function correctly implements combined filter logic with AND operation.');
    process.exit(0);
} else {
    console.log('❌ Some tests failed. Please review the implementation.');
    process.exit(1);
}
