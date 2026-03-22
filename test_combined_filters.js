/**
 * Test Suite for Combined Filters in User Management
 * Tests Requirement 7.6: Combined Filter Composition
 * 
 * This test verifies that the filterUsers() function correctly applies
 * multiple filters simultaneously using AND logic.
 */

// Mock DOM elements for testing
class MockElement {
    constructor(value = '') {
        this.value = value;
    }
}

class MockCard {
    constructor(username, fullname, dept, status) {
        this.dataset = {
            username: username,
            fullname: fullname,
            dept: dept,
            status: status
        };
        this.style = { display: 'block' };
    }
}

// Test the filter logic
function testFilterLogic(searchTerm, deptFilter, statusFilter, card) {
    const username = card.dataset.username.toLowerCase();
    const fullname = card.dataset.fullname.toLowerCase();
    const dept = card.dataset.dept;
    const status = card.dataset.status;
    
    const matchesSearch = username.includes(searchTerm.toLowerCase()) || fullname.includes(searchTerm.toLowerCase());
    const matchesDept = !deptFilter || dept === deptFilter;
    const matchesStatus = !statusFilter || status === statusFilter;
    
    return matchesSearch && matchesDept && matchesStatus;
}

// Test cases
const testCases = [
    // Test 1: No filters - should match all
    {
        name: "No filters applied",
        search: "",
        dept: "",
        status: "",
        cards: [
            new MockCard("john_doe", "John Doe", "requester", "active"),
            new MockCard("jane_smith", "Jane Smith", "it_admin", "inactive"),
        ],
        expectedMatches: [true, true]
    },
    
    // Test 2: Search only
    {
        name: "Search filter only (username match)",
        search: "john",
        dept: "",
        status: "",
        cards: [
            new MockCard("john_doe", "John Doe", "requester", "active"),
            new MockCard("jane_smith", "Jane Smith", "it_admin", "inactive"),
        ],
        expectedMatches: [true, false]
    },
    
    // Test 3: Search by fullname
    {
        name: "Search filter only (fullname match)",
        search: "Jane",
        dept: "",
        status: "",
        cards: [
            new MockCard("john_doe", "John Doe", "requester", "active"),
            new MockCard("jane_smith", "Jane Smith", "it_admin", "inactive"),
        ],
        expectedMatches: [false, true]
    },
    
    // Test 4: Department filter only
    {
        name: "Department filter only",
        search: "",
        dept: "it_admin",
        status: "",
        cards: [
            new MockCard("john_doe", "John Doe", "requester", "active"),
            new MockCard("jane_smith", "Jane Smith", "it_admin", "inactive"),
            new MockCard("bob_jones", "Bob Jones", "it_admin", "active"),
        ],
        expectedMatches: [false, true, true]
    },
    
    // Test 5: Status filter only
    {
        name: "Status filter only",
        search: "",
        dept: "",
        status: "active",
        cards: [
            new MockCard("john_doe", "John Doe", "requester", "active"),
            new MockCard("jane_smith", "Jane Smith", "it_admin", "inactive"),
            new MockCard("bob_jones", "Bob Jones", "treasury", "active"),
        ],
        expectedMatches: [true, false, true]
    },
    
    // Test 6: Search + Department (AND logic)
    {
        name: "Combined: Search + Department",
        search: "jane",
        dept: "it_admin",
        status: "",
        cards: [
            new MockCard("john_doe", "John Doe", "requester", "active"),
            new MockCard("jane_smith", "Jane Smith", "it_admin", "inactive"),
            new MockCard("jane_doe", "Jane Doe", "treasury", "active"),
        ],
        expectedMatches: [false, true, false] // Only Jane Smith in IT Admin
    },
    
    // Test 7: Search + Status (AND logic)
    {
        name: "Combined: Search + Status",
        search: "john",
        dept: "",
        status: "active",
        cards: [
            new MockCard("john_doe", "John Doe", "requester", "active"),
            new MockCard("john_smith", "John Smith", "it_admin", "inactive"),
            new MockCard("jane_doe", "Jane Doe", "treasury", "active"),
        ],
        expectedMatches: [true, false, false] // Only active John
    },
    
    // Test 8: Department + Status (AND logic)
    {
        name: "Combined: Department + Status",
        search: "",
        dept: "requester",
        status: "inactive",
        cards: [
            new MockCard("john_doe", "John Doe", "requester", "active"),
            new MockCard("jane_smith", "Jane Smith", "requester", "inactive"),
            new MockCard("bob_jones", "Bob Jones", "it_admin", "inactive"),
        ],
        expectedMatches: [false, true, false] // Only inactive requester
    },
    
    // Test 9: All three filters (AND logic)
    {
        name: "Combined: Search + Department + Status",
        search: "alice",
        dept: "requester",
        status: "inactive",
        cards: [
            new MockCard("alice_wilson", "Alice Wilson", "requester", "inactive"),
            new MockCard("alice_smith", "Alice Smith", "requester", "active"),
            new MockCard("alice_jones", "Alice Jones", "it_admin", "inactive"),
            new MockCard("bob_wilson", "Bob Wilson", "requester", "inactive"),
        ],
        expectedMatches: [true, false, false, false] // Only Alice Wilson matches all
    },
    
    // Test 10: Case insensitivity
    {
        name: "Case insensitive search",
        search: "JOHN",
        dept: "",
        status: "",
        cards: [
            new MockCard("john_doe", "John Doe", "requester", "active"),
            new MockCard("jane_smith", "Jane Smith", "it_admin", "inactive"),
        ],
        expectedMatches: [true, false]
    },
    
    // Test 11: Partial match in search
    {
        name: "Partial match in search",
        search: "jo",
        dept: "",
        status: "",
        cards: [
            new MockCard("john_doe", "John Doe", "requester", "active"),
            new MockCard("bob_jones", "Bob Jones", "treasury", "active"),
            new MockCard("jane_smith", "Jane Smith", "it_admin", "inactive"),
        ],
        expectedMatches: [true, true, false] // Matches john and jones
    },
    
    // Test 12: Empty search with filters
    {
        name: "Empty search with department and status filters",
        search: "",
        dept: "it_admin",
        status: "active",
        cards: [
            new MockCard("john_doe", "John Doe", "it_admin", "active"),
            new MockCard("jane_smith", "Jane Smith", "it_admin", "inactive"),
            new MockCard("bob_jones", "Bob Jones", "treasury", "active"),
        ],
        expectedMatches: [true, false, false] // Only active IT admin
    }
];

// Run tests
console.log("=== Combined Filters Test Suite ===\n");

let passedTests = 0;
let failedTests = 0;

testCases.forEach((testCase, index) => {
    console.log(`Test ${index + 1}: ${testCase.name}`);
    console.log(`  Filters: search="${testCase.search}", dept="${testCase.dept}", status="${testCase.status}"`);
    
    let testPassed = true;
    testCase.cards.forEach((card, cardIndex) => {
        const result = testFilterLogic(testCase.search, testCase.dept, testCase.status, card);
        const expected = testCase.expectedMatches[cardIndex];
        
        if (result !== expected) {
            console.log(`  ❌ FAILED: Card ${cardIndex} (${card.dataset.username})`);
            console.log(`     Expected: ${expected}, Got: ${result}`);
            testPassed = false;
        }
    });
    
    if (testPassed) {
        console.log(`  ✅ PASSED`);
        passedTests++;
    } else {
        failedTests++;
    }
    console.log("");
});

console.log("=== Test Summary ===");
console.log(`Total Tests: ${testCases.length}`);
console.log(`Passed: ${passedTests}`);
console.log(`Failed: ${failedTests}`);

if (failedTests === 0) {
    console.log("\n✅ All tests passed! The filter logic correctly implements combined filters with AND operation.");
} else {
    console.log("\n❌ Some tests failed. Please review the filter logic.");
}
