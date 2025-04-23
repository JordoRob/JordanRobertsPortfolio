### Wed, May 31 - 1hr
- Meeting with professor and tesm members

### Thu, Jun 1 - 2.5hr
- Working on the testing plan
    - description for all major types of testing
    - examples for all major types of testing


### Sat, Jun 3  - 5.5 hr
- Started working on sequence diagrams for all use cases
    - finished export csv, import csv, logout, undo, aloocate forman, edit employee

### Sun, Jun 4  - 6.5 hr
- Review and discuss the design doc with the team
- Finished all 21 sequence diagrams
    - added admin use cases in the design doc to reflect change in the use case diagram
    - adjusted account creation diagram to reflect change in the use case diagram


### Wed, Jun 7  - 1.5 hr
- Attend client meeting with the team


### Thu, Jun 8 - 5.5 hr
- Modifying and refining testing plan with Adam
- Team discussion about the design doc
- Team discussion about the presentation
- Editing presentation slides
- Practicing the presentation

### Sat, Jun 10 - 2 hr
- Initial implementation for login test and logout test
- Tried to figure out how to dockerie phpunit

### Tue, Jun 13 - 3.5 hr
- Refactor login/logout test
- Implementation for addEmployee, addEmployeetest
- Read through phpunit's documentation for database testing

### Wed, Jun 14 - 4 hr
- Team meeting and discussion after lecture time
- phpunit dependency setup, was following the tutorial Scott sent to Adam
    - phpunit stopped working if switch to another branch and switch back to the test setup branch
    - couldn't establish a connection to our web-server, says can't find driver

### Thu, Jun 15 - 1.5 hr
- Phpunit dependency setup in docker
   - referred to another source, set up the phpDockerfile
-Fixed LoginTest
    - relative path related issues in phpunit
-Fixed LogoutTest
    - relative path related issues in phpunit
    - 'Cannot start session when headers' issue, fixed by run in seperate process

## Fri, Jun 16 - 1 hr
- Peero Evaluations
- Weekly group meeting

## Sat, Jun 17 - 6 hr
- Implementation for edit job details
- Implementation for archive employee
- Implementation for Edit jobs
- Code refactor and clean up 
- Toying with phpunit, couldn't get anything to pass after the transaction thing
- Looked up import csv sample

## Tue, Jun 20 - 3 hr
- working on importcsv 
- discussion about clients' csv formatting issues, missing dates, etc

## Wed, Jun 21 - 2hr
- Set up phpunit based on adam's shared example repo from 360

## Thu, Jun 22 - 5hr 45min
- sql file for test
- Test cases for add employee
- Test cases for edit employee
- Test cases for delete employee
- Test cases for edit job
- Test cases for login
- Test cases for logout

## Fri, Jun 23 - 1hr
- Peer evaluation
- added more test cases, edge cases, refactor codes

## Sat, Jun 24 - 4 hr
- working on import jobs csv

## Mon, Jun 26 - 2hr 20min
- working on import jobs csv

## Tue, Jun 27 - 2hr
- implement add job
- working on import jobs csv

## Wed, Jun 28 - 2hr 40min
- design discussion
- working on import jobs csv

## Fri, Jun 30 - 3hr 30min
- peer evaluation
- update weekly logs
- working on import jobs csv, got basic stuff working

## Sun, Jul 2 - 1hr
- Debugging import csv

## Tue, Jul 4 - 4hr
- Made the test logs
- code refactor and debug

## Fri, Jul 7 - 1.5 hr
- Peer evaluation and meeting

## Sat, Jul 8 - 3.5 hr
- backup research and implementation
- cron didn't work so switched to sleep instead
- docker debug, adjust yaml file content

## Sun, Jul 9 - 3.5 hr
- backup implementation and debug
- manually tested backup
- auto delete backup files older than 30 days
- code review, fixed addEmployee test

## Tue, Jul 11 - 1.5 hr
- backup debug, fixed container load order
- load backup implementation

## Wed, Jul 12 - 1 hr
- Weekly meeting

## Thu, Jul 13 - 1.5 hr
- load backup

## Sat, Jul 15 - 5.5 hr
- load backup
- load backup tests
- admin view front end integration

## Sun, Jul 16 - 3.5 hr
- import csv bugfix

## Mon, Jul 17 - 3.25 hr
- import jobs csv
- load backup pr code adjustment

## Tue, Jul 18 - 2 hr
- import jobs csv almost finished

## Wed, Jul 19 - 2 hr
- Team meeting
- Team discussion after meeting
- debug move employee test

## Thu, Jul 20 - 3.5 hr
- added navbar for process csv upload
- import jobs complete
- admin page integration

## Sun, Jul 23 - 1hr
- Front end testing framework lookup

## Mon, Jul 24 - 4.2 hr
- Selenium setup
- Fixed import csv nav bar
- import csv test cases
- phpunit replative path fix

## Tue, Jul 25 - 4.5hr
- Selenium integration with phpunit
- Tried fixing phpwebdriver dependency issues

## WED, JUL 26 - 40 min
- revert import csv commit
- readd reverted codes

## THU, JUL 27, - 4.5 hr
- Delete employee frontedn
- Delete enployee backend refactor
- Download backup

## Sun, Jul 30 - 6.5 hr
- mannual backup front end completed
- mysql dump doesn't work in the web-server container, trying to figur out how to get it work

## Mon, Jul 31 - 9 hr
- mannual backup backend completed
- gave up on mysql dump and tried manually read everything in the db and put into a file
- foreign key constraint issues

## Tue, Aug 1 - 0.5 hr
- delete employee things
- test cases for manual backup
- working on manual load backup

## Wed, AUG 2 - 50 min
- manual load sql backup and tests etc

## Thu, Aug 3 - 2 hr
- code cleanup
- edit job detail test fix (not really fixed yet)
- import csv global table

## Fri, Aug 4 - 3.5 hr
- test O-Rama

## Sat, Aug 5 - 3.5 hr
- debug upload backup
- nothing working due to file_get_content() not handling some delimiter things

## Mon, Aug 7 - 3.5 hr
- attempt to fix uploadBackupTest, didn't work due to terminal dependency stuff
- archive job code review
- debugged backup stuff

## Tue, Aug 8 - 1.5 hr
- Edit final doc
- backup error message things

## Wed, Aug 9 - 6.5 hr
- client in person meeting and deployment 
- Class meeting

## Thu, Aug 10 - 4hr
- fixing old tests
- updated testdb and yaml volumes
- edit employee test and refactor into a function
- addjob test

## Fri, Aug 11 - 5.5 hr
- team meeting
- failed selenium setup
- test fixes
- practice presentation
- create manager tests

## Sat, Aug 12 - 3hr
- reset password test and refactor
- create manager tests, function refactor

## Sun, Aug 13 - 3hr
- final documentations
- test logs update
- change admin pw test

## Mon, Aug 14 - 5hr
- history screen code review
- test logs update
- use case update

## Tue, Aug 15 - 3hr
- UML diagrams, use case updates