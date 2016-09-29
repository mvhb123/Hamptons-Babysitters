//
//  JobHistoryCell.h
//  SitterApp
//
//  Created by Vikram gour on 15/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface JobHistoryCell : UITableViewCell

@property (weak, nonatomic) IBOutlet UIView *viewCellBg;
@property (weak, nonatomic)IBOutlet UILabel *lbl_jobNumber;
@property (weak, nonatomic)IBOutlet UILabel *lbl_jobAddress;
@property (weak, nonatomic)IBOutlet UILabel *lbl_startDate;
@property (weak, nonatomic)IBOutlet UILabel *lbl_endDate;
@property (weak, nonatomic)IBOutlet UILabel *lbl_ActualTime;
@property (weak, nonatomic)IBOutlet UILabel *lbl_rate;
@property (weak, nonatomic)IBOutlet UILabel *lbl_jobEarned;
@property (weak, nonatomic)IBOutlet UISwitch *sw_paidStatus;
@property (weak, nonatomic) IBOutlet UILabel *lbl_jobNumberHeading;
@property (weak, nonatomic) IBOutlet UILabel *lbl_startDateHeading;
@property (weak, nonatomic) IBOutlet UILabel *lbl_jobAddressHeading;
@property (weak, nonatomic) IBOutlet UILabel *lbl_endDateHeading;
@property (weak, nonatomic) IBOutlet UILabel *lbl_actualTimeHeading;
@property (weak, nonatomic) IBOutlet UILabel *lbl_rateHeading;
@property (weak, nonatomic) IBOutlet UILabel *lbl_jobEarnedHeading;
@property (weak, nonatomic) IBOutlet UILabel *lbl_paidHeading;
@property (weak, nonatomic) IBOutlet UIImageView *img_paidStatus;

@end
