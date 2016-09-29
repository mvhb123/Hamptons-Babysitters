//
//  JobListTableViewCell.h
//  SitterApp
//
//  Created by Shilpa Gade on 03/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface JobListTableViewCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UIView *viewCellBg;
@property (weak, nonatomic) IBOutlet UILabel *lbl_JobNumber;
@property (weak, nonatomic) IBOutlet UILabel *lbl_ShowJobNumber;
@property (weak, nonatomic) IBOutlet UILabel *lbl_JobStartDate;
@property (weak, nonatomic) IBOutlet UILabel *lbl_JobShowStartDate;
@property (weak, nonatomic) IBOutlet UILabel *lbl_JobEndDate;
@property (weak, nonatomic) IBOutlet UILabel *lbl_JobShowEndDate;
@property (weak, nonatomic) IBOutlet UILabel *lbl_ChildrenCount;
@property (weak, nonatomic) IBOutlet UILabel *lbl_ShowChildrenCount;
@property (weak, nonatomic) IBOutlet UILabel *lbl_ChildAge;
@property (weak, nonatomic) IBOutlet UILabel *lbl_ShowChildAge;
@property (weak, nonatomic) IBOutlet UILabel *lbl_area;
@property (weak, nonatomic) IBOutlet UILabel *lbl_showArea;

@end
