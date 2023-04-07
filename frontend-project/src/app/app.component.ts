import { Component, DefaultIterableDiffer, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { MatTableDataSource } from '@angular/material/table';
import { ConfirmDialogComponent } from './confirm-dialog/confirm-dialog.component';
import { User } from './interface/userData';
import { UserService } from './services/user.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
})
export class AppComponent {
  /**
   * Defining table data structure
   */
  dataSource = new MatTableDataSource<User>();
  title: string = 'GreenIT Application Challenge';

  /**
   * Creates an instance of app component.
   * @param dialog
   * @param userService
   */
  constructor(public dialog: MatDialog, private userService: UserService) {}

  /**
   * Sets table data on load
   * @param {void}
   * @return {void} returns nothing
   */
  ngOnInit() {
    this.resetData();
  }

  /**
   * Resets table data
   * @param {void}
   * @return {void} returns nothing
   */
  resetData() {
    this.userService.getUsers().subscribe((res : User[]) => {
      this.dataSource.data = res;
    })
  }

  /**
   * Creates field for new entry
   * @param {void}
   * @return {void} returns nothing
   */
  addRow() {
    const newRow: User = {
      id: 0,
      name: '',
      state: '',
      zip: '',
      amount: '',
      quantity: '',
      item: '',
      isEdit: true,
      isSelected: false,
    }
    this.dataSource.data = [newRow, ...this.dataSource.data]
  }

  /**
   * Removing multiple selected data from table
   * @param {void} 
   * @return {void} returns nothing
   */
  removeSelectedRows() {
    const users = this.dataSource.data.filter((u: User) => u.isSelected)
    
    if(users.length != 0) {
      // Shows confirmation dialog for selection
      this.dialog
      .open(ConfirmDialogComponent)
      .afterClosed()
      .subscribe((confirm) => {
        // If delete confirmed and shows messages
        if (confirm) {
          this.userService.deleteUsers(users).subscribe(() => {
            this.dataSource.data = this.dataSource.data.filter(
              (u: User) => !u.isSelected,
            )
          })

          this.userService.showSuccessMessage('Data removed successfully', 'Deleted');
          
        }
      })
    } else {
      // Shows error messages
      this.userService.showErrorMessage('Select any data', 'No data selected!');
      
    }
    
  }

}
